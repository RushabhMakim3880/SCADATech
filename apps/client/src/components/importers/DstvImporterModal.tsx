import React, { useState } from 'react';
import { ItemRecipe } from '@innovance-hmi/shared';
import { parseDstvNc1 } from '../../utils/dstvParser.js';
import { AngleBarVisualizer } from '../canvas/AngleBarVisualizer.js';
import {
  Upload,
  FileCode,
  CheckCircle,
  X,
  ArrowRight,
} from 'lucide-react';

interface DstvImporterModalProps {
  isOpen: boolean;
  onClose: () => void;
  onImportComplete: (recipe: ItemRecipe) => void;
}

export const DstvImporterModal: React.FC<DstvImporterModalProps> = ({
  isOpen,
  onClose,
  onImportComplete,
}) => {
  const [parsedRecipe, setParsedRecipe] = useState<ItemRecipe | null>(null);
  const [fileName, setFileName] = useState<string>('');
  const [dragOver, setDragOver] = useState(false);

  if (!isOpen) return null;

  const handleFileUpload = (file: File) => {
    setFileName(file.name);
    const reader = new FileReader();
    reader.onload = (e) => {
      const content = e.target?.result as string;
      if (content) {
        const recipe = parseDstvNc1(content, file.name);
        setParsedRecipe(recipe);
      }
    };
    reader.readAsText(file);
  };

  const handleSampleLoad = () => {
    const sampleNc1 = `ST
  TeklaStructures
  1
  TOWER-LEG-01
  L 75*75*6
  S355
  2
  1800.00
  75.00
  75.00
  6.00
  6.00
  0.00
  0.00
  0.00
  0.00
  0.00
BO
  v   150.00    35.00    18.00
  v   450.00    35.00    18.00
  v   850.00    35.00    18.00
  v  1400.00    35.00    18.00
  u   250.00    35.00    18.00
  u   650.00    35.00    18.00
  u  1100.00    35.00    18.00
SI
  v   600.00    35.00     0.00 TOWER-A15
EN`;
    const recipe = parseDstvNc1(sampleNc1, 'TOWER-LEG-01.nc1');
    setFileName('TOWER-LEG-01.nc1 (Sample CAD)');
    setParsedRecipe(recipe);
  };

  return (
    <div className="fixed inset-0 bg-slate-900/75 backdrop-blur-sm z-50 flex items-center justify-center p-4">
      <div className="bg-white rounded-lg shadow-2xl border border-slate-300 w-full max-w-4xl max-h-[90vh] flex flex-col overflow-hidden">
        {/* Modal Header */}
        <div className="panel-heading bg-slate-800 text-white px-4 py-3 flex items-center justify-between">
          <div className="flex items-center gap-2">
            <FileCode className="w-5 h-5 text-cyan-400" />
            <span className="font-bold text-sm">DSTV / NC1 CAD File Importer (Tekla / SDS2 / Bocad)</span>
          </div>
          <button onClick={onClose} className="text-slate-300 hover:text-white">
            <X className="w-5 h-5" />
          </button>
        </div>

        {/* Modal Body */}
        <div className="p-4 overflow-y-auto space-y-4 text-xs">
          {/* Drag and Drop Zone */}
          <div
            onDragOver={(e) => {
              e.preventDefault();
              setDragOver(true);
            }}
            onDragLeave={() => setDragOver(false)}
            onDrop={(e) => {
              e.preventDefault();
              setDragOver(false);
              if (e.dataTransfer.files.length > 0) {
                handleFileUpload(e.dataTransfer.files[0]);
              }
            }}
            className={`border-2 border-dashed rounded-lg p-6 text-center cursor-pointer transition-colors ${
              dragOver ? 'border-blue-500 bg-blue-50' : 'border-slate-300 bg-slate-50 hover:bg-slate-100'
            }`}
          >
            <input
              type="file"
              accept=".nc1,.nc,.dstv,.txt"
              id="fileInput"
              className="hidden"
              onChange={(e) => {
                if (e.target.files && e.target.files.length > 0) {
                  handleFileUpload(e.target.files[0]);
                }
              }}
            />
            <label htmlFor="fileInput" className="cursor-pointer flex flex-col items-center gap-2">
              <Upload className="w-8 h-8 text-blue-600" />
              <div>
                <span className="font-bold text-slate-800 text-sm">Drag & drop your Tekla DSTV (.nc1) file here</span>
                <span className="text-slate-500 block">or click to browse from your computer</span>
              </div>
            </label>

            <div className="mt-3 pt-3 border-t border-slate-200 flex items-center justify-center gap-2">
              <span className="text-slate-500">Need a quick test?</span>
              <button
                type="button"
                onClick={handleSampleLoad}
                className="btn-ca btn-ca-primary text-xs py-0.5 px-2"
              >
                Load Sample Transmission Tower NC1
              </button>
            </div>
          </div>

          {/* Parsed Result Preview */}
          {parsedRecipe && (
            <div className="space-y-3 pt-2">
              <div className="flex items-center justify-between p-3 bg-emerald-50 border border-emerald-300 rounded text-emerald-900">
                <div className="flex items-center gap-2 font-bold">
                  <CheckCircle className="w-4 h-4 text-emerald-600" />
                  <span>Successfully Parsed: {fileName}</span>
                </div>
                <div className="flex items-center gap-4 text-xs">
                  <span>Profile: <b>L{parsedRecipe.angleWidthA}x{parsedRecipe.angleWidthB}x{parsedRecipe.thickness}</b></span>
                  <span>Length: <b>{parsedRecipe.totalLength}mm</b></span>
                  <span>Steps: <b>{parsedRecipe.steps.length} Operations</b></span>
                </div>
              </div>

              {/* 2D CAD Preview of the Imported File */}
              <div className="h-64 border border-slate-300 rounded overflow-hidden">
                <AngleBarVisualizer recipe={parsedRecipe} />
              </div>
            </div>
          )}
        </div>

        {/* Modal Footer */}
        <div className="p-3 bg-slate-100 border-t border-slate-300 flex items-center justify-between">
          <button onClick={onClose} className="btn-ca btn-ca-default">
            Cancel
          </button>

          <button
            onClick={() => {
              if (parsedRecipe) {
                onImportComplete(parsedRecipe);
                onClose();
              }
            }}
            disabled={!parsedRecipe}
            className="btn-ca btn-ca-success"
          >
            <ArrowRight className="w-4 h-4" /> Load Imported Recipe into Editor
          </button>
        </div>
      </div>
    </div>
  );
};
