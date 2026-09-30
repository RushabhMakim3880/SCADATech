import React, { useRef, useEffect, useState, useCallback } from 'react';
import * as THREE from 'three';
import { ItemRecipe } from '@innovance-hmi/shared';
import {
  RotateCcw,
  Box,
  Compass,
  Eye,
  ShieldCheck,
  ArrowUp,
  ArrowDown,
  ArrowLeft,
  ArrowRight,
  ZoomIn,
  ZoomOut,
  Maximize2,
  Move,
  RotateCw,
  Navigation,
  ChevronDown,
  ChevronUp,
} from 'lucide-react';

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
  const [navMode, setNavMode] = useState<'PAN' | 'ORBIT'>('PAN');
  const [moveSpeed, setMoveSpeed] = useState<number>(1);
  const [isControlsCollapsed, setIsControlsCollapsed] = useState<boolean>(false);
  const holdIntervalRef = useRef<number | null>(null);

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
      // Direct Face-On View of Flange A (Vertical Flange)
      cameraRotationRef.current = { theta: 0, phi: Math.PI / 2, radius: Math.max(700, lengthMm * 0.65) };
      targetLookAtRef.current.set(lengthMm / 2, widthA / 2, 0);
    } else if (preset === 'SIDE') {
      // Direct Face-On View of Flange B (Horizontal Flange from bottom)
      cameraRotationRef.current = { theta: Math.PI / 2, phi: 0.05, radius: Math.max(700, lengthMm * 0.65) };
      targetLookAtRef.current.set(lengthMm / 2, 0, -widthB / 2);
    } else if (preset === 'PROFILE') {
      // End L-Profile Cross Section View
      cameraRotationRef.current = { theta: Math.PI, phi: Math.PI / 2, radius: Math.max(500, widthA * 4) };
      targetLookAtRef.current.set(0, widthA / 2, -widthB / 2);
    }

    updateCameraPosition();
  };

  const handleDirectionalMove = useCallback((dir: 'UP' | 'DOWN' | 'LEFT' | 'RIGHT') => {
    const camera = cameraRef.current;
    if (!camera) return;

    if (navMode === 'PAN') {
      const right = new THREE.Vector3(1, 0, 0).applyQuaternion(camera.quaternion);
      const up = new THREE.Vector3(0, 1, 0).applyQuaternion(camera.quaternion);
      const step = Math.max(30, cameraRotationRef.current.radius * 0.05) * moveSpeed;

      if (dir === 'LEFT') targetLookAtRef.current.addScaledVector(right, -step);
      if (dir === 'RIGHT') targetLookAtRef.current.addScaledVector(right, step);
      if (dir === 'UP') targetLookAtRef.current.addScaledVector(up, step);
      if (dir === 'DOWN') targetLookAtRef.current.addScaledVector(up, -step);
    } else {
      const rotStep = 0.08 * moveSpeed;
      if (dir === 'LEFT') cameraRotationRef.current.theta -= rotStep;
      if (dir === 'RIGHT') cameraRotationRef.current.theta += rotStep;
      if (dir === 'UP') cameraRotationRef.current.phi = Math.max(0.05, cameraRotationRef.current.phi - rotStep);
      if (dir === 'DOWN') cameraRotationRef.current.phi = Math.min(Math.PI - 0.05, cameraRotationRef.current.phi + rotStep);
    }

    updateCameraPosition();
    if (onViewportSync) {
      onViewportSync(Math.max(0, Math.min(lengthMm, targetLookAtRef.current.x)));
    }
  }, [navMode, moveSpeed, updateCameraPosition, onViewportSync, lengthMm]);

  const handleZoom = useCallback((direction: 'IN' | 'OUT') => {
    const factor = direction === 'IN' ? 0.82 : 1.22;
    cameraRotationRef.current.radius = Math.max(200, Math.min(25000, cameraRotationRef.current.radius * factor));
    updateCameraPosition();
  }, [updateCameraPosition]);

  const handleRecenter = useCallback(() => {
    targetLookAtRef.current.set(lengthMm / 2, widthA / 2, -widthB / 2);
    cameraRotationRef.current.radius = Math.max(900, lengthMm * 0.75);
    updateCameraPosition();
    if (onViewportSync) onViewportSync(lengthMm / 2);
  }, [lengthMm, widthA, widthB, updateCameraPosition, onViewportSync]);

  const handleJump = useCallback((fraction: number) => {
    targetLookAtRef.current.x = lengthMm * fraction;
    updateCameraPosition();
    if (onViewportSync) onViewportSync(lengthMm * fraction);
  }, [lengthMm, updateCameraPosition, onViewportSync]);

  const startContinuousAction = (action: () => void) => {
    action();
    if (holdIntervalRef.current) clearInterval(holdIntervalRef.current);
    holdIntervalRef.current = window.setInterval(action, 65);
  };

  const stopContinuousAction = () => {
    if (holdIntervalRef.current) {
      clearInterval(holdIntervalRef.current);
      holdIntervalRef.current = null;
    }
  };

  // Keyboard navigation support
  useEffect(() => {
    const handleKeyDown = (e: KeyboardEvent) => {
      const target = e.target as HTMLElement;
      if (target && (target.tagName === 'INPUT' || target.tagName === 'TEXTAREA' || target.tagName === 'SELECT')) {
        return;
      }

      if (e.key === 'ArrowLeft') {
        e.preventDefault();
        handleDirectionalMove('LEFT');
      } else if (e.key === 'ArrowRight') {
        e.preventDefault();
        handleDirectionalMove('RIGHT');
      } else if (e.key === 'ArrowUp') {
        e.preventDefault();
        handleDirectionalMove('UP');
      } else if (e.key === 'ArrowDown') {
        e.preventDefault();
        handleDirectionalMove('DOWN');
      } else if (e.key === '+' || e.key === '=') {
        e.preventDefault();
        handleZoom('IN');
      } else if (e.key === '-' || e.key === '_') {
        e.preventDefault();
        handleZoom('OUT');
      } else if (e.key === 'Home') {
        e.preventDefault();
        handleJump(0);
      } else if (e.key === 'End') {
        e.preventDefault();
        handleJump(1.0);
      }
    };

    window.addEventListener('keydown', handleKeyDown);
    return () => window.removeEventListener('keydown', handleKeyDown);
  }, [handleDirectionalMove, handleZoom, handleJump]);

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
            frontRim.position.set(0, 0, thickness / 2 + 0.1);
            backRim.position.set(0, 0, -thickness / 2 - 0.1);
            crossMesh.position.set(0, 0, thickness / 2 + 0.2);
          } else {
            holeGroup.position.set(step.xPosition, thickness / 2, -step.yPosition);
            rimGeom.rotateX(Math.PI / 2);
            frontRim.position.set(0, -thickness / 2 - 0.1, 0);
            backRim.position.set(0, thickness / 2 + 0.1, 0);
            crossGeom.rotateX(Math.PI / 2);
            crossMesh.position.set(0, -thickness / 2 - 0.2, 0);
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
      <div className="bg-[#0e141f] px-2 sm:px-3 py-1.5 border-b border-[#1c2738] flex flex-wrap items-center justify-between gap-2 z-10 shrink-0">
        <div className="flex flex-wrap items-center gap-1 sm:gap-1.5">
          <button
            type="button"
            onClick={() => setCameraPreset('ISO')}
            className={`btn-ca text-xs py-1 px-2 sm:px-2.5 flex items-center gap-1 font-bold ${
              activePreset === 'ISO' ? 'btn-ca-primary' : 'btn-ca-dark'
            }`}
          >
            <Box className="w-3.5 h-3.5" /> <span className="hidden sm:inline">3D </span><span>Iso</span>
          </button>
          <button
            type="button"
            onClick={() => setCameraPreset('TOP')}
            className={`btn-ca text-xs py-1 px-2 sm:px-2.5 flex items-center gap-1 font-bold ${
              activePreset === 'TOP' ? 'btn-ca-primary' : 'btn-ca-dark'
            }`}
          >
            <Compass className="w-3.5 h-3.5" /> <span>Flange A</span>
          </button>
          <button
            type="button"
            onClick={() => setCameraPreset('SIDE')}
            className={`btn-ca text-xs py-1 px-2 sm:px-2.5 flex items-center gap-1 font-bold ${
              activePreset === 'SIDE' ? 'btn-ca-primary' : 'btn-ca-dark'
            }`}
          >
            <Compass className="w-3.5 h-3.5" /> <span>Flange B</span>
          </button>
          <button
            type="button"
            onClick={() => setCameraPreset('PROFILE')}
            className={`btn-ca text-xs py-1 px-2 sm:px-2.5 flex items-center gap-1 font-bold ${
              activePreset === 'PROFILE' ? 'btn-ca-primary' : 'btn-ca-dark'
            }`}
          >
            <Eye className="w-3.5 h-3.5" /> <span>Profile</span>
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
        <div className="hidden sm:flex items-center gap-2 sm:gap-3 text-[11px] font-mono font-bold text-slate-300">
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

      {/* Floating Precision 3D Directional Controller D-Pad */}
      <div className="absolute bottom-3 right-3 z-20 flex flex-col items-end gap-1.5 pointer-events-auto select-none">
        {/* Toggle Collapse Button for Compact Displays */}
        <button
          type="button"
          onClick={() => setIsControlsCollapsed(!isControlsCollapsed)}
          className="p-1 px-2.5 rounded-lg bg-[#0c131f]/90 hover:bg-[#162337] border border-cyan-500/40 text-cyan-300 text-[11px] font-mono font-bold flex items-center gap-1.5 shadow-xl backdrop-blur-md transition-all active:scale-95"
          title="Toggle 3D Directional Controls"
        >
          <Navigation className="w-3.5 h-3.5 text-cyan-400" />
          <span>3D Nav Pad</span>
          {isControlsCollapsed ? <ChevronUp className="w-3.5 h-3.5 text-slate-400" /> : <ChevronDown className="w-3.5 h-3.5 text-slate-400" />}
        </button>

        {!isControlsCollapsed && (
          <div className="bg-[#0b121e]/95 border border-[#22354c] p-3 rounded-2xl shadow-2xl backdrop-blur-md flex flex-col items-center gap-2.5 w-48">
            {/* Mode Switch: Pan vs Orbit & Speed */}
            <div className="flex items-center justify-between w-full border-b border-[#1b2a3d] pb-2">
              <div className="flex items-center bg-[#070b12] rounded p-0.5 border border-[#1a293d]">
                <button
                  type="button"
                  onClick={() => setNavMode('PAN')}
                  className={`px-2 py-0.5 rounded text-[10px] font-bold flex items-center gap-1 transition-all ${
                    navMode === 'PAN' ? 'bg-cyan-600 text-white shadow' : 'text-slate-400 hover:text-white'
                  }`}
                  title="Pan / Move camera in 2D view space"
                >
                  <Move className="w-3 h-3" /> Pan
                </button>
                <button
                  type="button"
                  onClick={() => setNavMode('ORBIT')}
                  className={`px-2 py-0.5 rounded text-[10px] font-bold flex items-center gap-1 transition-all ${
                    navMode === 'ORBIT' ? 'bg-amber-600 text-white shadow' : 'text-slate-400 hover:text-white'
                  }`}
                  title="Orbit / Rotate camera angle around target"
                >
                  <RotateCw className="w-3 h-3" /> Orbit
                </button>
              </div>

              {/* Speed multiplier toggle */}
              <button
                type="button"
                onClick={() => setMoveSpeed((s) => (s === 1 ? 2.5 : 1))}
                className={`px-1.5 py-0.5 rounded text-[10px] font-mono font-bold border transition-colors ${
                  moveSpeed > 1
                    ? 'bg-amber-950/80 border-amber-500 text-amber-300'
                    : 'bg-[#121c2a] border-[#223348] text-slate-400'
                }`}
                title="Toggle Movement Speed (1x / 2.5x)"
              >
                {moveSpeed}x
              </button>
            </div>

            {/* Tactile Directional Diamond D-Pad */}
            <div className="relative w-28 h-28 flex items-center justify-center">
              {/* Up Button */}
              <button
                type="button"
                onMouseDown={() => startContinuousAction(() => handleDirectionalMove('UP'))}
                onMouseUp={stopContinuousAction}
                onMouseLeave={stopContinuousAction}
                onTouchStart={() => startContinuousAction(() => handleDirectionalMove('UP'))}
                onTouchEnd={stopContinuousAction}
                className="absolute top-0 w-8 h-8 rounded-lg bg-[#142030] hover:bg-cyan-900/60 active:bg-cyan-600 text-slate-200 hover:text-cyan-200 border border-[#273d5a] flex items-center justify-center shadow-md transition-all active:scale-95 cursor-pointer"
                title={navMode === 'PAN' ? 'Move Camera Up (Pan)' : 'Tilt Camera Up'}
              >
                <ArrowUp className="w-4 h-4" />
              </button>

              {/* Left Button */}
              <button
                type="button"
                onMouseDown={() => startContinuousAction(() => handleDirectionalMove('LEFT'))}
                onMouseUp={stopContinuousAction}
                onMouseLeave={stopContinuousAction}
                onTouchStart={() => startContinuousAction(() => handleDirectionalMove('LEFT'))}
                onTouchEnd={stopContinuousAction}
                className="absolute left-0 w-8 h-8 rounded-lg bg-[#142030] hover:bg-cyan-900/60 active:bg-cyan-600 text-slate-200 hover:text-cyan-200 border border-[#273d5a] flex items-center justify-center shadow-md transition-all active:scale-95 cursor-pointer"
                title={navMode === 'PAN' ? 'Move Camera Left (Towards 0mm Datum)' : 'Orbit Camera Left'}
              >
                <ArrowLeft className="w-4 h-4" />
              </button>

              {/* Center Recenter Button */}
              <button
                type="button"
                onClick={handleRecenter}
                className="w-7 h-7 rounded-full bg-[#18263a] hover:bg-cyan-600 text-cyan-300 hover:text-white border border-[#2d4566] flex items-center justify-center shadow-inner transition-all active:scale-90 cursor-pointer"
                title="Re-center view on Angle Bar Center"
              >
                <Maximize2 className="w-3.5 h-3.5" />
              </button>

              {/* Right Button */}
              <button
                type="button"
                onMouseDown={() => startContinuousAction(() => handleDirectionalMove('RIGHT'))}
                onMouseUp={stopContinuousAction}
                onMouseLeave={stopContinuousAction}
                onTouchStart={() => startContinuousAction(() => handleDirectionalMove('RIGHT'))}
                onTouchEnd={stopContinuousAction}
                className="absolute right-0 w-8 h-8 rounded-lg bg-[#142030] hover:bg-cyan-900/60 active:bg-cyan-600 text-slate-200 hover:text-cyan-200 border border-[#273d5a] flex items-center justify-center shadow-md transition-all active:scale-95 cursor-pointer"
                title={navMode === 'PAN' ? 'Move Camera Right (Towards Cut End)' : 'Orbit Camera Right'}
              >
                <ArrowRight className="w-4 h-4" />
              </button>

              {/* Down Button */}
              <button
                type="button"
                onMouseDown={() => startContinuousAction(() => handleDirectionalMove('DOWN'))}
                onMouseUp={stopContinuousAction}
                onMouseLeave={stopContinuousAction}
                onTouchStart={() => startContinuousAction(() => handleDirectionalMove('DOWN'))}
                onTouchEnd={stopContinuousAction}
                className="absolute bottom-0 w-8 h-8 rounded-lg bg-[#142030] hover:bg-cyan-900/60 active:bg-cyan-600 text-slate-200 hover:text-cyan-200 border border-[#273d5a] flex items-center justify-center shadow-md transition-all active:scale-95 cursor-pointer"
                title={navMode === 'PAN' ? 'Move Camera Down (Pan)' : 'Tilt Camera Down'}
              >
                <ArrowDown className="w-4 h-4" />
              </button>
            </div>

            {/* Zoom & Fast Length Jump Controls */}
            <div className="flex items-center justify-between w-full border-t border-[#1b2a3d] pt-2 gap-1.5">
              <div className="flex items-center gap-1">
                <button
                  type="button"
                  onMouseDown={() => startContinuousAction(() => handleZoom('IN'))}
                  onMouseUp={stopContinuousAction}
                  onMouseLeave={stopContinuousAction}
                  onTouchStart={() => startContinuousAction(() => handleZoom('IN'))}
                  onTouchEnd={stopContinuousAction}
                  className="w-7 h-7 rounded-lg bg-[#142030] hover:bg-[#1e2f46] active:bg-cyan-600 text-slate-300 hover:text-white border border-[#273d5a] flex items-center justify-center cursor-pointer shadow-sm"
                  title="Zoom In (+)"
                >
                  <ZoomIn className="w-3.5 h-3.5" />
                </button>
                <button
                  type="button"
                  onMouseDown={() => startContinuousAction(() => handleZoom('OUT'))}
                  onMouseUp={stopContinuousAction}
                  onMouseLeave={stopContinuousAction}
                  onTouchStart={() => startContinuousAction(() => handleZoom('OUT'))}
                  onTouchEnd={stopContinuousAction}
                  className="w-7 h-7 rounded-lg bg-[#142030] hover:bg-[#1e2f46] active:bg-cyan-600 text-slate-300 hover:text-white border border-[#273d5a] flex items-center justify-center cursor-pointer shadow-sm"
                  title="Zoom Out (-)"
                >
                  <ZoomOut className="w-3.5 h-3.5" />
                </button>
              </div>

              {/* Fast Along-Length Jumps */}
              <div className="flex items-center gap-1 font-mono text-[9px]">
                <button
                  type="button"
                  onClick={() => handleJump(0)}
                  className="px-1.5 py-1 rounded bg-[#142030] hover:bg-[#1e2f46] text-cyan-300 hover:text-white border border-[#23354d] cursor-pointer"
                  title="Jump to 0mm Datum (Front)"
                >
                  0m
                </button>
                <button
                  type="button"
                  onClick={() => handleJump(0.5)}
                  className="px-1.5 py-1 rounded bg-[#142030] hover:bg-[#1e2f46] text-cyan-300 hover:text-white border border-[#23354d] cursor-pointer"
                  title="Jump to Center (3008mm)"
                >
                  Mid
                </button>
                <button
                  type="button"
                  onClick={() => handleJump(1.0)}
                  className="px-1.5 py-1 rounded bg-[#142030] hover:bg-[#1e2f46] text-cyan-300 hover:text-white border border-[#23354d] cursor-pointer"
                  title="Jump to Cut-Off End (6016mm)"
                >
                  End
                </button>
              </div>
            </div>
          </div>
        )}
      </div>
    </div>
  );
};
