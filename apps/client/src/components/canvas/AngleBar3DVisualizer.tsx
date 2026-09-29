import React, { useRef, useEffect, useState, useCallback } from 'react';
import * as THREE from 'three';
import { ItemRecipe } from '@innovance-hmi/shared';
import { RotateCcw, Box, Compass, Eye, ShieldCheck } from 'lucide-react';

interface AngleBar3DVisualizerProps {
  recipe?: ItemRecipe | null;
  activeFeedPosition?: number;
  highlightStepIndex?: number;
  externalHoverStepIndex?: number | null;
  syncFocusX?: number | null;
  onSelectStep?: (stepIndex: number) => void;
  onHoverStep?: (stepIndex: number | null) => void;
  onViewportSync?: (centerMm: number) => void;
}

const DEFAULT_DEMO_RECIPE: ItemRecipe = {
  id: 'demo-recipe-3d',
  itemCode: 'NBS-601',
  itemName: 'GETCO 66kV Transmission Tower Leg',
  totalLength: 6016.0,
  angleWidthA: 150.0,
  angleWidthB: 150.0,
  thickness: 20.0,
  measurementType: 'ABSOLUTE',
  isActive: true,
  createdAt: new Date().toISOString(),
  updatedAt: new Date().toISOString(),
  steps: [],
};

export const AngleBar3DVisualizer: React.FC<AngleBar3DVisualizerProps> = ({
  recipe,
  activeFeedPosition = 0,
  highlightStepIndex,
  externalHoverStepIndex,
  syncFocusX,
  onSelectStep,
  onHoverStep,
  onViewportSync,
}) => {
  const containerRef = useRef<HTMLDivElement | null>(null);
  const canvasRef = useRef<HTMLCanvasElement | null>(null);

  const activeRecipe = recipe ? recipe : DEFAULT_DEMO_RECIPE;

  const lengthMm = activeRecipe.totalLength || 6016;
  const widthA = activeRecipe.angleWidthA || 150;
  const widthB = activeRecipe.angleWidthB || 150;
  const thickness = activeRecipe.thickness || 20;

  // Real structural steel metric calculations
  // Unit weight formula for equal/unequal angles: kg/m = (wA + wB - t) * t * 0.00785
  const weightPerMeter = ((widthA + widthB - thickness) * thickness * 0.00785).toFixed(2);
  const totalMassKg = (parseFloat(weightPerMeter) * (lengthMm / 1000)).toFixed(1);

  const sceneRef = useRef<THREE.Scene | null>(null);
  const cameraRef = useRef<THREE.PerspectiveCamera | null>(null);
  const rendererRef = useRef<THREE.WebGLRenderer | null>(null);
  const laserMeshRef = useRef<THREE.Group | null>(null);

  // Click-to-edit maps
  const interactiveMeshesRef = useRef<THREE.Mesh[]>([]);
  const meshToStepIndexMapRef = useRef<Map<string, number>>(new Map());

  const isDraggingRef = useRef(false);
  const isPanningRef = useRef(false);
  const previousMousePositionRef = useRef({ x: 0, y: 0 });
  const cameraRotationRef = useRef({
    theta: 0.85,
    phi: 0.65,
    radius: Math.max(900, lengthMm * 0.75),
  });
  const targetLookAtRef = useRef(new THREE.Vector3(lengthMm / 2, widthA / 2, -widthB / 2));

  const [activePreset, setActivePreset] = useState<'ISO' | 'TOP' | 'SIDE' | 'PROFILE'>('ISO');

  const updateCameraPosition = useCallback(() => {
    const camera = cameraRef.current;
    if (!camera) return;

    const { theta, phi, radius } = cameraRotationRef.current;
    const target = targetLookAtRef.current;

    camera.position.x = target.x + radius * Math.sin(phi) * Math.cos(theta);
    camera.position.y = target.y + radius * Math.cos(phi);
    camera.position.z = target.z + radius * Math.sin(phi) * Math.sin(theta);
    camera.lookAt(target);
  }, []);

  // Synchronize 3D horizontal focus with 2D Blueprint or Drawing Preview
  useEffect(() => {
    if (syncFocusX === null || syncFocusX === undefined) return;
    targetLookAtRef.current.x = syncFocusX;
    updateCameraPosition();
  }, [syncFocusX, updateCameraPosition]);

  useEffect(() => {
    if (highlightStepIndex !== undefined && activeRecipe.steps[highlightStepIndex]) {
      const step = activeRecipe.steps[highlightStepIndex];
      targetLookAtRef.current.x = step.xPosition;
      updateCameraPosition();
    }
  }, [highlightStepIndex, activeRecipe.steps, updateCameraPosition]);

  const setCameraPreset = (preset: 'ISO' | 'TOP' | 'SIDE' | 'PROFILE') => {
    setActivePreset(preset);
    const radius = Math.max(900, lengthMm * 0.75);

    if (preset === 'ISO') {
      cameraRotationRef.current = { theta: 0.85, phi: 0.65, radius };
      targetLookAtRef.current.set(lengthMm / 2, widthA / 2, -widthB / 2);
    } else if (preset === 'TOP') {
      // Direct Top View of Flange A
      cameraRotationRef.current = { theta: Math.PI / 2, phi: 0.05, radius: Math.max(800, lengthMm * 0.65) };
      targetLookAtRef.current.set(lengthMm / 2, 0, 0);
    } else if (preset === 'SIDE') {
      // Direct Side View of Flange B
      cameraRotationRef.current = { theta: 0, phi: Math.PI / 2, radius: Math.max(800, lengthMm * 0.65) };
      targetLookAtRef.current.set(lengthMm / 2, widthA / 2, 0);
    } else if (preset === 'PROFILE') {
      // End L-Profile Cross Section View
      cameraRotationRef.current = { theta: Math.PI, phi: Math.PI / 2, radius: Math.max(500, widthA * 4) };
      targetLookAtRef.current.set(0, widthA / 2, -widthB / 2);
    }

    updateCameraPosition();
  };

  useEffect(() => {
    const container = containerRef.current;
    const canvas = canvasRef.current;
    if (!container || !canvas) return;

    const scene = new THREE.Scene();
    scene.background = new THREE.Color(0x060a12);
    sceneRef.current = scene;

    const width = container.clientWidth;
    const height = container.clientHeight;
    const camera = new THREE.PerspectiveCamera(35, width / height, 1, 40000);
    cameraRef.current = camera;

    const renderer = new THREE.WebGLRenderer({
      canvas,
      antialias: true,
      powerPreference: 'high-performance',
    });
    renderer.setSize(width, height);
    renderer.setPixelRatio(Math.min(window.devicePixelRatio, 2));
    renderer.shadowMap.enabled = true;
    renderer.shadowMap.type = THREE.PCFSoftShadowMap;
    rendererRef.current = renderer;

    // Technical Blueprint Ambient & Studio Key Lighting
    const hemiLight = new THREE.HemisphereLight(0xffffff, 0x1e293b, 1.4);
    scene.add(hemiLight);

    const mainKeyLight = new THREE.DirectionalLight(0xffffff, 1.8);
    mainKeyLight.position.set(lengthMm / 2, 1800, 1600);
    mainKeyLight.castShadow = true;
    mainKeyLight.shadow.mapSize.width = 2048;
    mainKeyLight.shadow.mapSize.height = 2048;
    scene.add(mainKeyLight);

    const fillLight = new THREE.DirectionalLight(0x38bdf8, 1.1);
    fillLight.position.set(-800, 1000, -1000);
    scene.add(fillLight);

    const rimLight = new THREE.DirectionalLight(0x60a5fa, 0.9);
    rimLight.position.set(lengthMm + 800, -500, 800);
    scene.add(rimLight);

    // Grid Floor
    const gridHelper = new THREE.GridHelper(Math.max(3000, lengthMm * 1.4), 60, 0x38bdf8, 0x172554);
    gridHelper.position.set(lengthMm / 2, -2, -widthB / 2);
    scene.add(gridHelper);

    // Build True L-Angle Extrusion Shape
    const shape = new THREE.Shape();
    shape.moveTo(0, 0);
    shape.lineTo(widthB, 0);
    shape.lineTo(widthB, thickness);
    shape.lineTo(thickness, thickness);
    shape.lineTo(thickness, widthA);
    shape.lineTo(0, widthA);
    shape.closePath();

    const extrudeSettings = { steps: 1, depth: lengthMm, bevelEnabled: false };
    const geometry = new THREE.ExtrudeGeometry(shape, extrudeSettings);
    geometry.rotateY(Math.PI / 2); // Depth goes along positive X axis

    // High-realism Brushed Structural Steel PBR Material
    const steelMaterial = new THREE.MeshStandardMaterial({
      color: 0x94a3b8,
      metalness: 0.88,
      roughness: 0.24,
      wireframe: false,
    });

    const angleMesh = new THREE.Mesh(geometry, steelMaterial);
    angleMesh.castShadow = true;
    angleMesh.receiveShadow = true;
    scene.add(angleMesh);

    // Glowing CAD Edges
    const edgesGeom = new THREE.EdgesGeometry(geometry, 20);
    const edgesMat = new THREE.LineBasicMaterial({ color: 0x38bdf8, linewidth: 1.8 });
    const edgesMesh = new THREE.LineSegments(edgesGeom, edgesMat);
    scene.add(edgesMesh);

    // Central Bend Heel Line (Gold) in 3D
    const heelGeom = new THREE.BufferGeometry().setFromPoints([
      new THREE.Vector3(-20, 0, 0),
      new THREE.Vector3(lengthMm + 20, 0, 0),
    ]);
    const heelMat = new THREE.LineDashedMaterial({
      color: 0xf59e0b,
      dashSize: 15,
      gapSize: 6,
    });
    const heelLine = new THREE.Line(heelGeom, heelMat);
    heelLine.computeLineDistances();
    scene.add(heelLine);

    // Clear previous hit meshes
    interactiveMeshesRef.current = [];
    meshToStepIndexMapRef.current.clear();

    if (activeRecipe.steps) {
      activeRecipe.steps.forEach((step, idx) => {
        const isHighlight = highlightStepIndex === idx || externalHoverStepIndex === idx;
        const isDone = activeFeedPosition > step.xPosition;

        if (step.operationType === 'PUNCH') {
          const holeRadius = (step.toolSize || 17.5) / 2;
          const isSideA = step.side === 'A';

          let punchColor = isSideA ? 0x00e5ff : 0x10b981;
          if (isDone) punchColor = isSideA ? 0x083344 : 0x064e3b;

          const holeGroup = new THREE.Group();

          // True Cylindrical Bore
          const boreGeom = new THREE.CylinderGeometry(holeRadius, holeRadius, thickness + 2.0, 32, 1, false);
          const boreMat = new THREE.MeshStandardMaterial({
            color: 0x020617,
            roughness: 0.95,
            metalness: 0.1,
          });
          const boreMesh = new THREE.Mesh(boreGeom, boreMat);

          interactiveMeshesRef.current.push(boreMesh);
          meshToStepIndexMapRef.current.set(boreMesh.uuid, idx);
          holeGroup.add(boreMesh);

          // Glowing Technical Chamfer Rim Rings
          const rimGeom = new THREE.RingGeometry(holeRadius - 0.2, holeRadius + 2.8, 32);
          const rimMat = new THREE.MeshBasicMaterial({
            color: isHighlight ? 0xffffff : punchColor,
            side: THREE.DoubleSide,
          });
          const frontRim = new THREE.Mesh(rimGeom, rimMat);
          const backRim = new THREE.Mesh(rimGeom, rimMat);

          interactiveMeshesRef.current.push(frontRim);
          meshToStepIndexMapRef.current.set(frontRim.uuid, idx);

          // Glowing High-Visibility Selection Ring in 3D
          if (isHighlight) {
            const glowGeom = new THREE.RingGeometry(holeRadius + 1.2, holeRadius + 6.0, 32);
            const glowMat = new THREE.MeshBasicMaterial({
              color: 0xfbbf24,
              side: THREE.DoubleSide,
            });
            const glowMesh = new THREE.Mesh(glowGeom, glowMat);
            if (isSideA) {
              glowMesh.position.set(0, 0, -thickness / 2 - 0.2);
            } else {
              glowGeom.rotateX(Math.PI / 2);
              glowMesh.position.set(0, thickness / 2 + 0.2, 0);
            }
            holeGroup.add(glowMesh);
          }

          // Technical Drafting Crosshairs in 3D
          const crossGeom = new THREE.BufferGeometry();
          const arm = holeRadius + 4.0;
          const crossVertices = new Float32Array([
            -arm, 0, 0,  arm, 0, 0,
            0, -arm, 0,  0, arm, 0,
          ]);
          crossGeom.setAttribute('position', new THREE.BufferAttribute(crossVertices, 3));
          const crossMat = new THREE.LineBasicMaterial({ color: isHighlight ? 0xffffff : punchColor });
          const crossMesh = new THREE.LineSegments(crossGeom, crossMat);

          if (isSideA) {
            boreGeom.rotateX(Math.PI / 2);
            holeGroup.position.set(step.xPosition, step.yPosition, -thickness / 2);
            frontRim.position.set(0, 0, -thickness / 2 - 0.05);
            backRim.position.set(0, 0, thickness / 2 + 0.05);
            crossMesh.position.set(0, 0, -thickness / 2 - 0.1);
          } else {
            holeGroup.position.set(step.xPosition, thickness / 2, -step.yPosition);
            rimGeom.rotateX(Math.PI / 2);
            frontRim.position.set(0, thickness / 2 + 0.05, 0);
            backRim.position.set(0, -thickness / 2 - 0.05, 0);
            crossGeom.rotateX(Math.PI / 2);
            crossMesh.position.set(0, thickness / 2 + 0.1, 0);
          }

          holeGroup.add(frontRim);
          holeGroup.add(backRim);
          holeGroup.add(crossMesh);
          scene.add(holeGroup);
        } else if (step.operationType === 'MARK') {
          // Marking Stamping Cassette Block
          const markGeom = new THREE.BoxGeometry(45, 16, 1.2);
          const markMat = new THREE.MeshStandardMaterial({
            color: isDone ? 0x78350f : 0xf59e0b,
            metalness: 0.5,
            roughness: 0.3,
          });
          const markMesh = new THREE.Mesh(markGeom, markMat);
          markMesh.position.set(step.xPosition, step.yPosition, -thickness - 0.6);
          scene.add(markMesh);
        } else if (step.operationType === 'CUT' || step.isCutOff) {
          // Hydraulic Shear Cut-Off Plane
          const cutProfile = new THREE.Shape();
          cutProfile.moveTo(-5, -5);
          cutProfile.lineTo(widthB + 10, -5);
          cutProfile.lineTo(widthB + 10, thickness + 5);
          cutProfile.lineTo(thickness + 5, thickness + 5);
          cutProfile.lineTo(thickness + 5, widthA + 10);
          cutProfile.lineTo(-5, widthA + 10);
          cutProfile.closePath();

          const cutGeom = new THREE.BufferGeometry().setFromPoints(cutProfile.getPoints());
          const cutMat = new THREE.LineBasicMaterial({ color: isDone ? 0x9f1239 : 0xff3366, linewidth: 3.0 });
          const cutMesh = new THREE.LineLoop(cutGeom, cutMat);
          cutMesh.rotateY(Math.PI / 2);
          cutMesh.position.set(step.xPosition, 0, 0);
          scene.add(cutMesh);
        }
      });
    }

    // 3D Moving Feed Laser Carriage Plane
    const laserGroup = new THREE.Group();
    const laserProfile = new THREE.Shape();
    laserProfile.moveTo(-10, -10);
    laserProfile.lineTo(widthB + 15, -10);
    laserProfile.lineTo(widthB + 15, thickness + 10);
    laserProfile.lineTo(thickness + 10, thickness + 10);
    laserProfile.lineTo(thickness + 10, widthA + 15);
    laserProfile.lineTo(-10, widthA + 15);
    laserProfile.closePath();

    const laserGeom = new THREE.BufferGeometry().setFromPoints(laserProfile.getPoints());
    const laserMat = new THREE.LineBasicMaterial({ color: 0x00ffcc, linewidth: 2.5 });
    const laserLine = new THREE.LineLoop(laserGeom, laserMat);
    laserLine.rotateY(Math.PI / 2);
    laserGroup.add(laserLine);

    laserGroup.position.set(activeFeedPosition, 0, 0);
    laserMeshRef.current = laserGroup;
    scene.add(laserGroup);

    updateCameraPosition();

    // Render loop
    let animationFrameId: number;
    const animate = () => {
      animationFrameId = requestAnimationFrame(animate);
      if (laserMeshRef.current) {
        laserMeshRef.current.position.x = activeFeedPosition;
      }
      renderer.render(scene, camera);
    };
    animate();

    const handleResize = () => {
      if (!container || !renderer || !camera) return;
      const w = container.clientWidth;
      const h = container.clientHeight;
      camera.aspect = w / h;
      camera.updateProjectionMatrix();
      renderer.setSize(w, h);
    };

    window.addEventListener('resize', handleResize);

    return () => {
      cancelAnimationFrame(animationFrameId);
      window.removeEventListener('resize', handleResize);
      renderer.dispose();
      geometry.dispose();
      steelMaterial.dispose();
    };
  }, [activeRecipe, lengthMm, widthA, widthB, thickness, activeFeedPosition, highlightStepIndex, updateCameraPosition]);

  // Orbit controls mouse interactions
  const handleMouseDown = (e: React.MouseEvent<HTMLCanvasElement>) => {
    if (e.button === 2) {
      isPanningRef.current = true;
    } else {
      isDraggingRef.current = true;
    }
    previousMousePositionRef.current = { x: e.clientX, y: e.clientY };
  };

  const handleMouseMove = (e: React.MouseEvent<HTMLCanvasElement>) => {
    const deltaX = e.clientX - previousMousePositionRef.current.x;
    const deltaY = e.clientY - previousMousePositionRef.current.y;
    previousMousePositionRef.current = { x: e.clientX, y: e.clientY };

    if (isDraggingRef.current) {
      cameraRotationRef.current.theta += deltaX * 0.006;
      cameraRotationRef.current.phi = Math.max(0.05, Math.min(Math.PI - 0.05, cameraRotationRef.current.phi - deltaY * 0.006));
      updateCameraPosition();
    } else if (isPanningRef.current) {
      const panFactor = 0.8;
      targetLookAtRef.current.x -= deltaX * panFactor;
      targetLookAtRef.current.y += deltaY * panFactor;
      updateCameraPosition();
      if (onViewportSync) {
        onViewportSync(Math.max(0, Math.min(lengthMm, targetLookAtRef.current.x)));
      }
    } else if (onHoverStep && canvasRef.current && cameraRef.current) {
      const canvas = canvasRef.current;
      const rect = canvas.getBoundingClientRect();
      const mouseX = e.clientX - rect.left;
      const mouseY = e.clientY - rect.top;
      const mouse = new THREE.Vector2(
        (mouseX / rect.width) * 2 - 1,
        -(mouseY / rect.height) * 2 + 1
      );
      const raycaster = new THREE.Raycaster();
      raycaster.setFromCamera(mouse, cameraRef.current);
      const intersects = raycaster.intersectObjects(interactiveMeshesRef.current);
      if (intersects.length > 0) {
        const mesh = intersects[0].object as THREE.Mesh;
        const stepIdx = meshToStepIndexMapRef.current.get(mesh.uuid);
        if (stepIdx !== undefined) {
          onHoverStep(stepIdx);
        }
      } else {
        onHoverStep(null);
      }
    }
  };

  const handleMouseUp = (e: React.MouseEvent<HTMLCanvasElement>) => {
    const isClick = Math.abs(e.clientX - previousMousePositionRef.current.x) < 4 && Math.abs(e.clientY - previousMousePositionRef.current.y) < 4;
    isDraggingRef.current = false;
    isPanningRef.current = false;

    if (isClick && onSelectStep && cameraRef.current && sceneRef.current) {
      const canvas = canvasRef.current;
      if (!canvas) return;
      const rect = canvas.getBoundingClientRect();
      const mouse = new THREE.Vector2(
        ((e.clientX - rect.left) / rect.width) * 2 - 1,
        -((e.clientY - rect.top) / rect.height) * 2 + 1
      );

      const raycaster = new THREE.Raycaster();
      raycaster.setFromCamera(mouse, cameraRef.current);
      const intersects = raycaster.intersectObjects(interactiveMeshesRef.current, false);

      if (intersects.length > 0) {
        const hitMesh = intersects[0].object as THREE.Mesh;
        const stepIdx = meshToStepIndexMapRef.current.get(hitMesh.uuid);
        if (stepIdx !== undefined) {
          onSelectStep(stepIdx);
        }
      }
    }
  };

  // Non-passive wheel event listener for 3D camera zoom without passive event listener warnings
  useEffect(() => {
    const canvas = canvasRef.current;
    if (!canvas) return;

    const onWheelNative = (e: WheelEvent) => {
      e.preventDefault();
      const factor = e.deltaY < 0 ? 0.9 : 1.1;
      cameraRotationRef.current.radius = Math.max(200, Math.min(25000, cameraRotationRef.current.radius * factor));
      updateCameraPosition();
    };

    canvas.addEventListener('wheel', onWheelNative, { passive: false });
    return () => {
      canvas.removeEventListener('wheel', onWheelNative);
    };
  }, []);

  return (
    <div ref={containerRef} className="relative w-full h-full flex flex-col bg-[#060a12] select-none rounded-lg overflow-hidden border border-slate-700">
      {/* 3D Viewport Toolbar */}
      <div className="bg-[#0e141f] px-3 py-1.5 border-b border-[#1c2738] flex items-center justify-between z-10 shrink-0">
        <div className="flex items-center gap-1.5">
          <button
            type="button"
            onClick={() => setCameraPreset('ISO')}
            className={`btn-ca text-xs py-1 px-2.5 flex items-center gap-1 font-bold ${
              activePreset === 'ISO' ? 'btn-ca-primary' : 'btn-ca-dark'
            }`}
          >
            <Box className="w-3.5 h-3.5" /> 3D Isometric
          </button>
          <button
            type="button"
            onClick={() => setCameraPreset('TOP')}
            className={`btn-ca text-xs py-1 px-2.5 flex items-center gap-1 font-bold ${
              activePreset === 'TOP' ? 'btn-ca-primary' : 'btn-ca-dark'
            }`}
          >
            <Compass className="w-3.5 h-3.5" /> Top (Flange A)
          </button>
          <button
            type="button"
            onClick={() => setCameraPreset('SIDE')}
            className={`btn-ca text-xs py-1 px-2.5 flex items-center gap-1 font-bold ${
              activePreset === 'SIDE' ? 'btn-ca-primary' : 'btn-ca-dark'
            }`}
          >
            <Compass className="w-3.5 h-3.5" /> Side (Flange B)
          </button>
          <button
            type="button"
            onClick={() => setCameraPreset('PROFILE')}
            className={`btn-ca text-xs py-1 px-2.5 flex items-center gap-1 font-bold ${
              activePreset === 'PROFILE' ? 'btn-ca-primary' : 'btn-ca-dark'
            }`}
          >
            <Eye className="w-3.5 h-3.5" /> L-Section
          </button>
          <button
            type="button"
            onClick={() => setCameraPreset('ISO')}
            title="Reset 3D Camera"
            className="btn-ca btn-ca-dark text-xs py-1 px-2"
          >
            <RotateCcw className="w-3.5 h-3.5" />
          </button>
        </div>

        {/* Real Engineering Data HUD */}
        <div className="flex items-center gap-3 text-[11px] font-mono font-bold text-slate-300">
          <span>Section: <strong className="text-sky-300">L{widthA}X{widthB}X{thickness}</strong></span>
          <span className="text-slate-600">|</span>
          <span>Unit: <strong className="text-emerald-400">{weightPerMeter} kg/m</strong></span>
          <span className="text-slate-600">|</span>
          <span>Total Mass: <strong className="text-amber-400">{totalMassKg} kg</strong></span>
        </div>
      </div>

      <canvas
        ref={canvasRef}
        onMouseDown={handleMouseDown}
        onMouseMove={handleMouseMove}
        onMouseUp={handleMouseUp}
        onContextMenu={(e) => e.preventDefault()}
        className="w-full flex-1 cursor-grab active:cursor-grabbing min-h-[300px]"
      />

      {/* Floating 2D Cross-Section Schematic HUD Inset */}
      <div className="absolute bottom-3 left-3 bg-[#0a0f18]/90 backdrop-blur-md p-3 rounded-lg border border-[#1e2d42] shadow-2xl pointer-events-none z-10 flex items-center gap-4">
        {/* SVG L-Profile Schematic */}
        <svg width="60" height="60" viewBox="0 0 60 60" className="shrink-0">
          <path
            d="M 10 50 L 50 50 L 50 42 L 18 42 L 18 10 L 10 10 Z"
            fill="rgba(56, 189, 248, 0.25)"
            stroke="#38bdf8"
            strokeWidth="1.8"
          />
          {/* Dimension arrows */}
          <line x1="10" y1="54" x2="50" y2="54" stroke="#94a3b8" strokeWidth="1" />
          <line x1="6" y1="10" x2="6" y2="50" stroke="#94a3b8" strokeWidth="1" />
        </svg>

        <div className="font-mono text-[11px] leading-tight space-y-0.5">
          <div className="text-sky-400 font-extrabold flex items-center gap-1">
            <ShieldCheck className="w-3.5 h-3.5 text-emerald-400" />
            L{widthA} × {widthB} × {thickness} mm
          </div>
          <div className="text-slate-400 text-[10px]">Transmission Line Angle</div>
          <div className="text-slate-300">
            Length: <span className="text-white font-bold">{lengthMm} mm</span>
          </div>
          <div className="text-emerald-400 text-[10px]">
            Steel Grade: IS 2062 E250 / E350
          </div>
        </div>
      </div>
    </div>
  );
};
