import React, { useRef, useEffect } from 'react';
import * as THREE from 'three';
import { ItemRecipe } from '@innovance-hmi/shared';
import { RotateCcw, Box } from 'lucide-react';

interface AngleBar3DVisualizerProps {
  recipe?: ItemRecipe | null;
  activeFeedPosition?: number;
  highlightStepIndex?: number;
  onSelectStep?: (stepIndex: number) => void;
}

const DEFAULT_DEMO_RECIPE: ItemRecipe = {
  id: 'demo-recipe-3d',
  itemCode: 'L75x75x6 - 1500mm',
  itemName: 'Transmission Tower Standard Angle',
  totalLength: 1500.0,
  angleWidthA: 75.0,
  angleWidthB: 75.0,
  thickness: 6.0,
  measurementType: 'ABSOLUTE',
  isActive: true,
  createdAt: new Date().toISOString(),
  updatedAt: new Date().toISOString(),
  steps: [
    { id: 's1', stepNumber: 1, operationType: 'PUNCH', side: 'A', xPosition: 150.0, yPosition: 35.0, toolSize: 18.0, isCutOff: false },
    { id: 's2', stepNumber: 2, operationType: 'PUNCH', side: 'A', xPosition: 400.0, yPosition: 35.0, toolSize: 18.0, isCutOff: false },
    { id: 's3', stepNumber: 3, operationType: 'MARK', side: 'A', xPosition: 600.0, yPosition: 35.0, markingText: 'T1-L15', isCutOff: false },
    { id: 's4', stepNumber: 4, operationType: 'PUNCH', side: 'B', xPosition: 250.0, yPosition: 35.0, toolSize: 18.0, isCutOff: false },
    { id: 's5', stepNumber: 5, operationType: 'PUNCH', side: 'B', xPosition: 500.0, yPosition: 35.0, toolSize: 18.0, isCutOff: false },
    { id: 's6', stepNumber: 6, operationType: 'PUNCH', side: 'B', xPosition: 1200.0, yPosition: 35.0, toolSize: 18.0, isCutOff: false },
    { id: 's7', stepNumber: 7, operationType: 'CUT', side: 'NA', xPosition: 1500.0, yPosition: 0, isCutOff: true },
  ],
};

export const AngleBar3DVisualizer: React.FC<AngleBar3DVisualizerProps> = ({
  recipe,
  activeFeedPosition = 0,
  highlightStepIndex,
  onSelectStep,
}) => {
  const containerRef = useRef<HTMLDivElement | null>(null);
  const canvasRef = useRef<HTMLCanvasElement | null>(null);

  const activeRecipe = recipe ? recipe : DEFAULT_DEMO_RECIPE;

  const lengthMm = activeRecipe.totalLength || 1500;
  const widthA = activeRecipe.angleWidthA || 75;
  const widthB = activeRecipe.angleWidthB || 75;
  const thickness = activeRecipe.thickness || 6;

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
  const cameraRotationRef = useRef({ theta: 0.75, phi: 0.60, radius: Math.max(800, lengthMm * 0.8) });
  const targetLookAtRef = useRef(new THREE.Vector3(lengthMm / 2, widthA / 2, -widthB / 2));

  useEffect(() => {
    const container = containerRef.current;
    const canvas = canvasRef.current;
    if (!container || !canvas) return;

    const scene = new THREE.Scene();
    scene.background = new THREE.Color(0x0f172a);
    sceneRef.current = scene;

    const width = container.clientWidth;
    const height = container.clientHeight;
    const camera = new THREE.PerspectiveCamera(35, width / height, 1, 30000);
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

    const hemiLight = new THREE.HemisphereLight(0xffffff, 0x334155, 1.2);
    scene.add(hemiLight);

    const mainKeyLight = new THREE.DirectionalLight(0xffffff, 1.4);
    mainKeyLight.position.set(lengthMm / 2, 1200, 1500);
    scene.add(mainKeyLight);

    const fillLight = new THREE.DirectionalLight(0x93c5fd, 0.9);
    fillLight.position.set(-600, 800, -800);
    scene.add(fillLight);

    const bottomLight = new THREE.DirectionalLight(0x64748b, 0.7);
    bottomLight.position.set(lengthMm / 2, -800, 600);
    scene.add(bottomLight);

    const gridHelper = new THREE.GridHelper(Math.max(2500, lengthMm * 1.5), 50, 0x38bdf8, 0x1e293b);
    gridHelper.position.set(lengthMm / 2, -1, -widthB / 2);
    scene.add(gridHelper);

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
    // Rotate +90 degrees around Y so the depth (Z) goes along positive X
    geometry.rotateY(Math.PI / 2);

    const steelMaterial = new THREE.MeshStandardMaterial({
      color: 0x94a3b8, 
      metalness: 0.82,
      roughness: 0.28,
      transparent: true,
    });

    const angleMesh = new THREE.Mesh(geometry, steelMaterial);
    angleMesh.castShadow = true;
    angleMesh.receiveShadow = true;
    scene.add(angleMesh);

    const edgesGeom = new THREE.EdgesGeometry(geometry, 20);
    const edgesMat = new THREE.LineBasicMaterial({ color: 0x38bdf8, linewidth: 1.5 });
    const edgesMesh = new THREE.LineSegments(edgesGeom, edgesMat);
    scene.add(edgesMesh);

    // Clear previous hit meshes
    interactiveMeshesRef.current = [];
    meshToStepIndexMapRef.current.clear();

    if (activeRecipe.steps) {
      const cuts = activeRecipe.steps.filter(s => s.operationType === 'CUT' || s.isCutOff);

      activeRecipe.steps.forEach((step, idx) => {
        const isHighlight = highlightStepIndex === idx;
        const isDone = activeFeedPosition > step.xPosition;

        if (step.operationType === 'PUNCH') {
          const holeRadius = (step.toolSize || 18) / 2;
          const isSideA = step.side === 'A';
          
          let punchColor = isSideA ? 0x00e5ff : 0x00e676;
          if (isDone) punchColor = isSideA ? 0x083344 : 0x064e3b; // Ghosting color in 3D

          const holeGroup = new THREE.Group();

          const boreGeom = new THREE.CylinderGeometry(holeRadius, holeRadius, thickness + 1.5, 32, 1, false);
          const boreMat = new THREE.MeshStandardMaterial({
            color: 0x020617,
            roughness: 0.95,
            metalness: 0.1,
          });
          const boreMesh = new THREE.Mesh(boreGeom, boreMat);
          
          // Make bore mesh interactive for raycasting click-to-edit
          interactiveMeshesRef.current.push(boreMesh);
          meshToStepIndexMapRef.current.set(boreMesh.uuid, idx);

          holeGroup.add(boreMesh);

          const rimGeom = new THREE.RingGeometry(holeRadius - 0.2, holeRadius + 2.5, 32);
          const rimMat = new THREE.MeshBasicMaterial({
            color: isHighlight ? 0xffffff : punchColor,
            side: THREE.DoubleSide,
          });
          const frontRim = new THREE.Mesh(rimGeom, rimMat);
          const backRim = new THREE.Mesh(rimGeom, rimMat);
          
          interactiveMeshesRef.current.push(frontRim);
          meshToStepIndexMapRef.current.set(frontRim.uuid, idx);

          const crossGeom = new THREE.BufferGeometry();
          const arm = holeRadius + 3.5;
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

          // 3D Collision Zone Warning
          const isCollision = cuts.some(cut => Math.abs(cut.xPosition - step.xPosition) < 20);
          if (isCollision) {
            const warningGeom = new THREE.BoxGeometry(30, widthA + 20, widthB + 20);
            const warningMat = new THREE.MeshBasicMaterial({ color: 0xef4444, transparent: true, opacity: 0.15, depthWrite: false });
            const warningBox = new THREE.Mesh(warningGeom, warningMat);
            // Center the box around the origin of the holeGroup (which is local 0,0,0)
            warningBox.position.set(0, 0, 0); 
            holeGroup.add(warningBox);
          }

          scene.add(holeGroup);
        } else if (step.operationType === 'MARK') {
          const markGeom = new THREE.BoxGeometry(45, 14, 0.6);
          const markMat = new THREE.MeshStandardMaterial({
            color: isDone ? 0x78350f : 0xf59e0b,
            emissive: isDone ? 0x000000 : 0xf59e0b,
            emissiveIntensity: 0.4,
          });
          const markMesh = new THREE.Mesh(markGeom, markMat);
          markMesh.position.set(step.xPosition, step.yPosition, -0.3);
          
          interactiveMeshesRef.current.push(markMesh);
          meshToStepIndexMapRef.current.set(markMesh.uuid, idx);

          scene.add(markMesh);
        } else if (step.operationType === 'CUT' || step.isCutOff) {
          const cutProfile = new THREE.Shape();
          cutProfile.moveTo(-2, -2);
          cutProfile.lineTo(widthB + 4, -2);
          cutProfile.lineTo(widthB + 4, thickness + 3);
          cutProfile.lineTo(thickness + 3, thickness + 3);
          cutProfile.lineTo(thickness + 3, widthA + 4);
          cutProfile.lineTo(-2, widthA + 4);
          cutProfile.closePath();

          const cutGeom = new THREE.BufferGeometry().setFromPoints(cutProfile.getPoints());
          cutGeom.rotateY(Math.PI / 2);
          const cutMat = new THREE.LineBasicMaterial({ color: isDone ? 0x9f1239 : 0xff3366, linewidth: 2.5 });
          const cutMesh = new THREE.LineLoop(cutGeom, cutMat);
          cutMesh.position.set(step.xPosition, 0, 0);
          scene.add(cutMesh);
        }
      });
    }

    const laserGroup = new THREE.Group();
    const laserProfile = new THREE.Shape();
    laserProfile.moveTo(-4, -4);
    laserProfile.lineTo(widthB + 6, -4);
    laserProfile.lineTo(widthB + 6, thickness + 6);
    laserProfile.lineTo(thickness + 6, thickness + 6);
    laserProfile.lineTo(thickness + 6, widthA + 6);
    laserProfile.lineTo(-4, widthA + 6);
    laserProfile.closePath();

    const laserGeom = new THREE.BufferGeometry().setFromPoints(laserProfile.getPoints());
    laserGeom.rotateY(Math.PI / 2);
    const laserMat = new THREE.LineBasicMaterial({ color: 0x00ffcc, linewidth: 2.5 });
    const laserRing = new THREE.LineLoop(laserGeom, laserMat);
    laserGroup.add(laserRing);

    laserGroup.position.set(activeFeedPosition, 0, 0);
    scene.add(laserGroup);
    laserMeshRef.current = laserGroup;

    const axesHelper = new THREE.AxesHelper(90);
    axesHelper.position.set(0, 0, 0);
    scene.add(axesHelper);

    const updateCameraPos = () => {
      const { theta, phi, radius } = cameraRotationRef.current;
      const target = targetLookAtRef.current;

      camera.position.x = target.x + radius * Math.sin(theta) * Math.cos(phi);
      camera.position.y = target.y + radius * Math.sin(phi);
      camera.position.z = target.z + radius * Math.cos(theta) * Math.cos(phi);
      camera.lookAt(target);
    };

    updateCameraPos();

    let animationFrameId: number;
    const animate = () => {
      animationFrameId = requestAnimationFrame(animate);
      if (laserMeshRef.current) {
        laserMeshRef.current.position.x = activeFeedPosition;
      }
      renderer.render(scene, camera);
    };
    animate();

    const resizeObserver = new ResizeObserver(() => {
      if (!container || !renderer || !camera) return;
      const newW = container.clientWidth;
      const newH = container.clientHeight;
      camera.aspect = newW / newH;
      camera.updateProjectionMatrix();
      renderer.setSize(newW, newH);
    });
    resizeObserver.observe(container);

    return () => {
      cancelAnimationFrame(animationFrameId);
      resizeObserver.disconnect();
      renderer.dispose();
    };
  }, [activeRecipe, lengthMm, widthA, widthB, thickness, highlightStepIndex, activeFeedPosition]);

  // Click-to-edit raycasting
  const handleCanvasClick = (e: React.MouseEvent) => {
    if (!onSelectStep || isDraggingRef.current || isPanningRef.current) return;
    const canvas = canvasRef.current;
    const camera = cameraRef.current;
    if (!canvas || !camera) return;

    const rect = canvas.getBoundingClientRect();
    const mouse = new THREE.Vector2();
    mouse.x = ((e.clientX - rect.left) / rect.width) * 2 - 1;
    mouse.y = -((e.clientY - rect.top) / rect.height) * 2 + 1;

    const raycaster = new THREE.Raycaster();
    raycaster.setFromCamera(mouse, camera);

    const intersects = raycaster.intersectObjects(interactiveMeshesRef.current);
    if (intersects.length > 0) {
      const hitMesh = intersects[0].object;
      const stepIdx = meshToStepIndexMapRef.current.get(hitMesh.uuid);
      if (stepIdx !== undefined) {
        onSelectStep(stepIdx);
      }
    }
  };

  const handleMouseDown = (e: React.MouseEvent) => {
    if (e.button === 0) {
      isDraggingRef.current = true;
    } else if (e.button === 2) {
      isPanningRef.current = true;
    }
    previousMousePositionRef.current = { x: e.clientX, y: e.clientY };
  };

  const handleMouseMove = (e: React.MouseEvent) => {
    const deltaX = e.clientX - previousMousePositionRef.current.x;
    const deltaY = e.clientY - previousMousePositionRef.current.y;
    previousMousePositionRef.current = { x: e.clientX, y: e.clientY };

    if (isDraggingRef.current) {
      cameraRotationRef.current.theta -= deltaX * 0.007;
      cameraRotationRef.current.phi = Math.max(
        0.05,
        Math.min(Math.PI / 2 - 0.05, cameraRotationRef.current.phi + deltaY * 0.007)
      );
      updateCamera();
    } else if (isPanningRef.current) {
      targetLookAtRef.current.x -= deltaX * 1.5;
      targetLookAtRef.current.y += deltaY * 1.5;
      updateCamera();
    }
  };

  const handleMouseUp = () => {
    // Delay setting false slightly so click handler doesn't trigger if it was a drag
    setTimeout(() => {
      isDraggingRef.current = false;
      isPanningRef.current = false;
    }, 10);
  };

  const handleWheel = (e: React.WheelEvent) => {
    e.preventDefault();
    cameraRotationRef.current.radius = Math.max(
      250,
      Math.min(6000, cameraRotationRef.current.radius + e.deltaY * 1.2)
    );
    updateCamera();
  };

  const updateCamera = () => {
    const camera = cameraRef.current;
    if (!camera) return;
    const { theta, phi, radius } = cameraRotationRef.current;
    const target = targetLookAtRef.current;

    camera.position.x = target.x + radius * Math.sin(theta) * Math.cos(phi);
    camera.position.y = target.y + radius * Math.sin(phi);
    camera.position.z = target.z + radius * Math.cos(theta) * Math.cos(phi);
    camera.lookAt(target);
  };

  const setPresetView = (view: 'ISO' | 'TOP' | 'FRONT' | 'CROSS') => {
    targetLookAtRef.current.set(lengthMm / 2, widthA / 2, -widthB / 2);
    if (view === 'ISO') {
      cameraRotationRef.current = { theta: 0.75, phi: 0.60, radius: Math.max(800, lengthMm * 0.8) };
    } else if (view === 'TOP') {
      cameraRotationRef.current = { theta: 0, phi: Math.PI / 2 - 0.05, radius: Math.max(800, lengthMm * 0.8) };
    } else if (view === 'FRONT') {
      cameraRotationRef.current = { theta: 0, phi: 0.05, radius: Math.max(800, lengthMm * 0.8) };
    } else if (view === 'CROSS') {
      cameraRotationRef.current = { theta: -Math.PI / 2, phi: 0.25, radius: 320 };
    }
    updateCamera();
  };

  return (
    <div
      ref={containerRef}
      onContextMenu={(e) => e.preventDefault()}
      onMouseDown={handleMouseDown}
      onMouseMove={handleMouseMove}
      onMouseUp={handleMouseUp}
      onMouseLeave={handleMouseUp}
      onClick={handleCanvasClick}
      onWheel={handleWheel}
      className="relative w-full h-full flex flex-col bg-[#0f172a] rounded overflow-hidden select-none border border-slate-700 cursor-grab active:cursor-grabbing"
    >
      <div className="absolute top-2 left-2 z-10 flex items-center gap-1.5 bg-[#1e293b]/90 backdrop-blur-sm p-1 rounded border border-slate-700">
        <button onClick={() => setPresetView('ISO')} className="btn-ca btn-ca-dark text-xs py-1 px-2 font-bold" title="Isometric 3D View">
          <Box className="w-3.5 h-3.5" /> 3D ISO
        </button>
        <button onClick={() => setPresetView('TOP')} className="btn-ca btn-ca-dark text-xs py-1 px-2" title="Top View (Flange B)">
          Top Flange (B)
        </button>
        <button onClick={() => setPresetView('FRONT')} className="btn-ca btn-ca-dark text-xs py-1 px-2" title="Front View (Flange A)">
          Front Flange (A)
        </button>
        <button onClick={() => setPresetView('CROSS')} className="btn-ca btn-ca-dark text-xs py-1 px-2" title="Cross-Section End Profile">
          End Profile
        </button>
        <button onClick={() => setPresetView('ISO')} className="btn-ca btn-ca-dark text-xs py-1 px-2" title="Reset Camera">
          <RotateCcw className="w-3.5 h-3.5" />
        </button>
      </div>

      <div className="absolute bottom-2 left-2 z-10 bg-[#1e293b]/90 backdrop-blur-sm px-3 py-1 rounded border border-slate-700 text-[10px] text-slate-300 flex items-center gap-3">
        <span className="flex items-center gap-1.5"><span className="w-2.5 h-2.5 rounded-full bg-[#00e5ff] inline-block" /> Flange A (DA1-3)</span>
        <span className="flex items-center gap-1.5"><span className="w-2.5 h-2.5 rounded-full bg-[#00e676] inline-block" /> Flange B (DB1-3)</span>
        <span className="flex items-center gap-1.5"><span className="w-2.5 h-2.5 rounded bg-[#f59e0b] inline-block" /> Marking</span>
        <span className="flex items-center gap-1.5"><span className="w-3 h-0.5 bg-[#ff3366] inline-block" /> Shear Line</span>
      </div>

      <canvas ref={canvasRef} className="w-full flex-1" />
    </div>
  );
};
