import { spawn } from 'child_process';
import path from 'path';
import fs from 'fs';
import os from 'os';
import { fileURLToPath } from 'url';

const __filename = fileURLToPath(import.meta.url);
const __dirname = path.dirname(__filename);

export interface CadParseResult {
  success: boolean;
  recipe?: any;
  metadata?: any;
  ruleChecks?: any;
  previewImage?: string;
  originalImage?: string;
  error?: string;
}

export async function parseCadDrawing(
  fileName: string,
  base64Data: string
): Promise<CadParseResult> {
  const tempDir = path.join(os.tmpdir(), 'hmi_cad_uploads');
  if (!fs.existsSync(tempDir)) {
    fs.mkdirSync(tempDir, { recursive: true });
  }

  // Strip data URL prefix if present
  const cleanBase64 = base64Data.replace(/^data:[^;]+;base64,/, '');
  const buffer = Buffer.from(cleanBase64, 'base64');

  const safeFileName = `${Date.now()}_${path.basename(fileName)}`;
  const filePath = path.join(tempDir, safeFileName);
  fs.writeFileSync(filePath, buffer);

  const pythonScript = path.resolve(__dirname, '../../scripts/cad_drawing_parser.py');

  return new Promise<CadParseResult>((resolve) => {
    const pythonProcess = spawn('python', [pythonScript, filePath], {
      windowsHide: true,
    });

    let stdout = '';
    let stderr = '';

    pythonProcess.stdout.on('data', (chunk) => {
      stdout += chunk.toString();
    });

    pythonProcess.stderr.on('data', (chunk) => {
      stderr += chunk.toString();
    });

    pythonProcess.on('close', (code) => {
      // Clean up temp upload file
      try {
        if (fs.existsSync(filePath)) {
          fs.unlinkSync(filePath);
        }
      } catch (err) {
        // ignore cleanup error
      }

      if (code !== 0) {
        console.error('CAD Drawing Parser error:', stderr);
        return resolve({
          success: false,
          error: stderr || `Python extractor exited with code ${code}`,
        });
      }

      try {
        const parsed = JSON.parse(stdout);
        resolve(parsed);
      } catch (e: any) {
        console.error('Failed to parse Python JSON output:', stdout.slice(0, 300));
        resolve({
          success: false,
          error: `Failed to parse extractor JSON: ${e.message}`,
        });
      }
    });

    pythonProcess.on('error', (err) => {
      resolve({
        success: false,
        error: `Failed to spawn Python process: ${err.message}`,
      });
    });
  });
}
