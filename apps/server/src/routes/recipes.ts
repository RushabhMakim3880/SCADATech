import { FastifyPluginAsync } from 'fastify';
import { prisma } from '../db/prisma.js';
import { parseCadDrawing } from '../importers/cadDrawingParser.js';

export const recipeRoutes: FastifyPluginAsync = async (fastify) => {
  // GET /api/recipes - List all recipes
  fastify.get('/recipes', async (request, reply) => {
    const recipes = await prisma.itemRecipe.findMany({
      include: {
        steps: {
          orderBy: { stepNumber: 'asc' },
        },
      },
      orderBy: { updatedAt: 'desc' },
    });
    return reply.send({ success: true, data: recipes });
  });

  // GET /api/recipes/:id
  fastify.get<{ Params: { id: string } }>('/recipes/:id', async (request, reply) => {
    const recipe = await prisma.itemRecipe.findUnique({
      where: { id: request.params.id },
      include: {
        steps: {
          orderBy: { stepNumber: 'asc' },
        },
      },
    });

    if (!recipe) {
      return reply.status(404).send({ success: false, error: 'Recipe not found' });
    }

    return reply.send({ success: true, data: recipe });
  });

  // POST /api/recipes - Create Recipe with Steps
  fastify.post<{
    Body: {
      itemCode: string;
      itemName: string;
      description?: string;
      angleWidthA: number;
      angleWidthB: number;
      thickness: number;
      totalLength: number;
      measurementType?: string;
      steps: Array<{
        stepNumber: number;
        operationType: string;
        side: string;
        xPosition: number;
        yPosition: number;
        toolSize?: number;
        toolShape?: string;
        markingText?: string;
        markingCassetteIndex?: number;
        isCutOff?: boolean;
        remarks?: string;
      }>;
    };
  }>('/recipes', async (request, reply) => {
    const { id, createdAt, updatedAt, steps, ...recipeData } = request.body as any;

    const newRecipe = await prisma.itemRecipe.create({
      data: {
        ...recipeData,
        steps: {
          create: (steps || []).map((s: any, idx: number) => {
            const { id: _stepId, recipeId: _recId, ...stepData } = s;
            return {
              ...stepData,
              stepNumber: s.stepNumber || idx + 1,
            };
          }),
        },
      },
      include: {
        steps: {
          orderBy: { stepNumber: 'asc' },
        },
      },
    });

    return reply.status(201).send({ success: true, data: newRecipe });
  });

  // PUT /api/recipes/:id - Update recipe & replace steps
  fastify.put<{
    Params: { id: string };
    Body: {
      itemCode?: string;
      itemName?: string;
      description?: string;
      angleWidthA?: number;
      angleWidthB?: number;
      thickness?: number;
      totalLength?: number;
      measurementType?: string;
      steps?: Array<{
        stepNumber: number;
        operationType: string;
        side: string;
        xPosition: number;
        yPosition: number;
        toolSize?: number;
        toolShape?: string;
        markingText?: string;
        markingCassetteIndex?: number;
        isCutOff?: boolean;
        remarks?: string;
      }>;
    };
  }>('/recipes/:id', async (request, reply) => {
    const { id } = request.params;
    const { steps, ...recipeData } = request.body;

    // Execute atomic update
    const updated = await prisma.$transaction(async (tx) => {
      if (steps) {
        // Delete old steps and insert new steps
        await tx.itemRecipeStep.deleteMany({ where: { recipeId: id } });
        await tx.itemRecipeStep.createMany({
          data: steps.map((s, idx) => ({
            ...s,
            recipeId: id,
            stepNumber: s.stepNumber || idx + 1,
          })),
        });
      }

      return tx.itemRecipe.update({
        where: { id },
        data: recipeData,
        include: {
          steps: {
            orderBy: { stepNumber: 'asc' },
          },
        },
      });
    });

    return reply.send({ success: true, data: updated });
  });

  // DELETE /api/recipes/:id
  fastify.delete<{ Params: { id: string } }>('/recipes/:id', async (request, reply) => {
    await prisma.itemRecipe.delete({
      where: { id: request.params.id },
    });
    return reply.send({ success: true, message: 'Recipe deleted' });
  });

  // POST /api/recipes/upload-drawing - Parse CAD / PDF / DXF Drawing
  fastify.post<{
    Body: {
      fileName: string;
      fileData: string; // base64 string
    };
  }>('/recipes/upload-drawing', async (request, reply) => {
    try {
      const { fileName, fileData } = request.body;
      if (!fileName || !fileData) {
        return reply.status(400).send({ success: false, error: 'fileName and fileData are required' });
      }

      const result = await parseCadDrawing(fileName, fileData);
      return reply.send(result);
    } catch (err: any) {
      console.error('Error in upload-drawing:', err);
      return reply.status(500).send({ success: false, error: err.message });
    }
  });

  // GET /api/recipes/sample-drawing - Load built-in sample TEST_R.pdf
  fastify.get('/recipes/sample-drawing', async (_request, reply) => {
    try {
      const fs = await import('fs');
      const path = await import('path');
      const candidatePaths = [
        path.resolve(process.cwd(), 'TEST_R.pdf'),
        path.resolve(process.cwd(), 'apps/server/TEST_R.pdf'),
        path.resolve(process.cwd(), '../TEST_R.pdf'),
        path.resolve(process.cwd(), '../../TEST_R.pdf'),
        'C:\\Users\\Rushabh Makim\\Downloads\\TEST_R.pdf',
        'D:\\Mindstien\\hptinnovanceanglepunchinghead6.test\\TEST_R.pdf'
      ];
      const samplePath = candidatePaths.find((p) => fs.existsSync(p));
      if (!samplePath) {
        return reply.status(404).send({ success: false, error: 'TEST_R.pdf sample not found in workspace' });
      }

      const fileBuf = fs.readFileSync(samplePath);
      const b64 = fileBuf.toString('base64');
      const result = await parseCadDrawing('TEST_R.pdf', b64);
      return reply.send(result);
    } catch (err: any) {
      return reply.status(500).send({ success: false, error: err.message });
    }
  });
};
