import { FastifyPluginAsync } from 'fastify';
import { prisma } from '../db/prisma.js';

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
    const { steps, ...recipeData } = request.body;

    const newRecipe = await prisma.itemRecipe.create({
      data: {
        ...recipeData,
        steps: {
          create: steps.map((s, idx) => ({
            ...s,
            stepNumber: s.stepNumber || idx + 1,
          })),
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
};
