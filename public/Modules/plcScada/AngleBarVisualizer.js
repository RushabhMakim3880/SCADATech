class AngleBarVisualizer {
    constructor(containerId, scale = 1) {
        this.containerId = containerId;
        this.stage = null;
        this.baseY = 0;
        this.layer = null;
        this.barConfig = {};
        this.tooltip = null;
        this.tooltipContainer = null;
        this.scale = scale;
        this.yOrientation = 'mirrored'; // 'normal' | 'mirrored'
        this.sideAThickness = 0;
        this.sideBThickness = 0;
        this.expandableHeight = 500;

        this.sideALabel = null;
        this.sideBLabel = null;


        // add to constructor
        this.contentGroup = null;
        this.lineAboveRef = null;
        this.lineBelowRef = null;


        this.colorPalette = [
            '#3cb44b', '#0082c8', '#f58231', '#911eb4', '#f032e6', '#AD1457', '#6A1B9A', '#00695C', '#EF6C00', '#4E342E', '#37474F'
        ];
        this.colorIndex = 0;
        this.punchColorMap = {};
        this.markerColorMap = {};
        this.currentBarEndX = 0;

        this.allShapes = [];
        this.lastSelected = null;
        this.defaultHeight = 100; // Set a default height
        this.isExpanded = false;
    }

    // new method
    applyOrientation() {
        const centerY = this.baseY + this.barConfig.upHeight * this.scale;

        // flip only drawing group around center line
        if (this.yOrientation === 'mirrored') {
            this.contentGroup.offsetY(centerY);
            this.contentGroup.y(centerY);
            this.contentGroup.scaleY(-1);
        } else {
            this.contentGroup.offsetY(0);
            this.contentGroup.y(0);
            this.contentGroup.scaleY(1);
        }

        // dashed vs solid helper lines
        const dashStyle = (this.yOrientation === 'mirrored') ? [5, 5] : [];
        if (this.lineAboveRef) this.lineAboveRef.dash(dashStyle);
        if (this.lineBelowRef) this.lineBelowRef.dash(dashStyle);

        // labels text
        this.updateSideLabels();

        // this.contentGroup.find('Text').each(node => {
        //     node.scaleY(this.yOrientation === 'mirrored' ? 1 : -1);
        // });
        this.layer.batchDraw();
    }

    // add method in class
    updateSideLabels() {
        if (!this.sideALabel || !this.sideBLabel) return;
        const topText = (this.yOrientation === 'mirrored') ? 'Side B' : 'Side A';
        const bottomText = (this.yOrientation === 'mirrored') ? 'Side A' : 'Side B';
        this.sideALabel.text(topText);
        this.sideBLabel.text(bottomText);
        this.layer.batchDraw();
    }

    // helper methods (add to class)
    setYOrientation(mode = 'normal') {
        this.yOrientation = (mode === 'mirrored') ? 'mirrored' : 'normal';
        // this.updateSideLabels();
        if (this.contentGroup) this.applyOrientation();

    }
    toggleYOrientation() {
        this.setYOrientation(this.yOrientation === 'normal' ? 'mirrored' : 'normal');
    }

    _clientToStagePoint(clientX, clientY) {
        const rect = this.stage.container().getBoundingClientRect();
        return {
            x: clientX - rect.left,
            y: clientY - rect.top
        };
    }

    toggleCanvasHeight() {
        const container = document.getElementById(this.containerId);
        if (!container) return;

        if (!this.isExpanded) {
            container.style.height = this.expandableHeight + "px"; // expanded height for zoom/pan
            this.isExpanded = true;
        } else {
            container.style.height = this.defaultHeight + "px"; // default height
            this.isExpanded = false;
        }

        // update stage size
        this.stage.height(container.offsetHeight);
        this.stage.width(container.offsetWidth);
        this.resetView();
    }


    // replace your getYPosition with this
    getYPosition(yMm, side) {
        const sepY = this.baseY + this.barConfig.upHeight * this.scale;
        if (side === 'A') return sepY - (yMm * this.scale);
        if (side === 'B') return sepY + (yMm * this.scale);
        return sepY;
    }



    getFitHeightScale() {
        const containerHeight = this.stage.height();
        const barHeightPx = (this.barConfig.upHeight + this.barConfig.bottomHeight) * this.scale;

        return containerHeight / barHeightPx;
    }

    initBar(lengthMm, upSideHeightMm, bottomSideHeightMm) {

        // if (this.yOrientation == 'mirrored') {
        //     const tempHeight = upSideHeightMm;
        //     upSideHeightMm = bottomSideHeightMm;
        //     bottomSideHeightMm = tempHeight;
        // }

        const container = document.getElementById(this.containerId);

        const fixedHeight = 100;
        container.style.height = fixedHeight + "px";

        const width = container.offsetWidth;
        const height = fixedHeight;

        this.stage = new Konva.Stage({
            container: this.containerId,
            width: width,
            height: height,
            draggable: true,
        });

        this.layer = new Konva.Layer();
        this.stage.add(this.layer);

        this.barConfig = {
            length: lengthMm,
            upHeight: upSideHeightMm,
            bottomHeight: bottomSideHeightMm
        };

        this.contentGroup = new Konva.Group();
        this.layer.add(this.contentGroup);



        const sideA = new Konva.Rect({
            x: 0,
            y: this.baseY,
            width: lengthMm * this.scale,
            height: this.barConfig.upHeight * this.scale,
            fill: '#ddd',
            stroke: 'black',
            strokeWidth: 1
        });

        const sideB = new Konva.Rect({
            x: 0,
            y: this.baseY + this.barConfig.upHeight * this.scale,
            width: lengthMm * this.scale,
            height: this.barConfig.bottomHeight * this.scale,
            fill: '#eee',
            stroke: 'black',
            strokeWidth: 1
        });

        const separator = new Konva.Line({
            points: [
                0, this.baseY + this.barConfig.upHeight * this.scale,
                lengthMm * this.scale, this.baseY + this.barConfig.upHeight * this.scale
            ],
            stroke: 'black',
            strokeWidth: 2,
        });

        this.contentGroup.add(sideA);
        this.contentGroup.add(sideB);
        this.contentGroup.add(separator);

        // helper lines (store refs)
        const centerY = this.baseY + this.barConfig.upHeight * this.scale;
        const dashStyle = (this.yOrientation === 'mirrored') ? [5, 5] : [];

        this.lineAboveRef = new Konva.Line({
            points: [0, centerY - this.sideBThickness, lengthMm * this.scale, centerY - this.sideBThickness],
            stroke: 'gray', strokeWidth: 1, dash: dashStyle
        });
        this.lineBelowRef = new Konva.Line({
            points: [0, centerY + this.sideAThickness, lengthMm * this.scale, centerY + this.sideAThickness],
            stroke: 'gray', strokeWidth: 1, dash: dashStyle
        });
        this.contentGroup.add(this.lineAboveRef);
        this.contentGroup.add(this.lineBelowRef);

        const centerTopY = this.baseY + (this.barConfig.upHeight * this.scale) / 2;
        const centerBottomY = this.baseY + this.barConfig.upHeight * this.scale + (this.barConfig.bottomHeight * this.scale) / 2;

        const topText = (this.yOrientation === 'mirrored') ? 'Side B' : 'Side A';
        const bottomText = (this.yOrientation === 'mirrored') ? 'Side A' : 'Side B';

        // create labels at left edge, vertically rotated and centered
        this.sideALabel = new Konva.Text({
            x: 10,                      // 10px from left edge
            y: centerTopY,              // vertical center of top half
            text: topText,
            fontSize: 18,
            fontFamily: 'Calibri',
            fill: '#999',
            rotation: -90,
        });
        this.sideALabel.offsetX(this.sideALabel.width() / 2);
        this.sideALabel.offsetY(this.sideALabel.height() / 2);

        this.sideBLabel = new Konva.Text({
            x: 10,
            y: centerBottomY,           // vertical center of bottom half
            text: bottomText,
            fontSize: 18,
            fontFamily: 'Calibri',
            fill: '#999',
            rotation: -90,
        });
        this.sideBLabel.offsetX(this.sideBLabel.width() / 2);
        this.sideBLabel.offsetY(this.sideBLabel.height() / 2);

        this.layer.add(this.sideALabel);
        this.layer.add(this.sideBLabel);


        // apply orientation transform now
        this.applyOrientation();
        this.layer.draw();

        this.stage.on('wheel', (e) => {
            e.evt.preventDefault();
            const oldScale = this.stage.scaleX();
            const pointer = this.stage.getPointerPosition();
            const scaleBy = 1.2;
            let newScale = e.evt.deltaY > 0 ? oldScale / scaleBy : oldScale * scaleBy;
            newScale = Math.max(0.1, Math.min(5, newScale));

            this.stage.scale({ x: newScale, y: newScale });

            const mousePointTo = {
                x: (pointer.x - this.stage.x()) / oldScale,
                y: (pointer.y - this.stage.y()) / oldScale,
            };

            const newPos = {
                x: pointer.x - mousePointTo.x * newScale,
                y: pointer.y - mousePointTo.y * newScale,
            };

            this.stage.position(newPos);
            this.stage.batchDraw();
        });

        let lastDist = 0;

        let isPinching = false;
        this.stage.getContent().addEventListener('touchstart', (e) => {
            if (e.touches.length === 2) {
                isPinching = true;
                this.stage.draggable(false);
                lastDist = 0;
            }
        }, { passive: false });

        this.stage.getContent().addEventListener('touchmove', (e) => {
            if (e.touches.length === 2) {
                e.preventDefault();

                const [t1, t2] = [e.touches[0], e.touches[1]];
                const dist = Math.hypot(t2.clientX - t1.clientX, t2.clientY - t1.clientY);

                // center point BETWEEN the two fingers, converted to stage coords
                const cx = (t1.clientX + t2.clientX) / 2;
                const cy = (t1.clientY + t2.clientY) / 2;
                const pointer = this._clientToStagePoint(cx, cy);

                const oldScale = this.stage.scaleX();
                if (!lastDist) {
                    lastDist = dist;
                    // store initial anchor mapping to content space
                    this._pinchAnchor = {
                        x: (pointer.x - this.stage.x()) / oldScale,
                        y: (pointer.y - this.stage.y()) / oldScale
                    };
                    return;
                }

                const scaleBy = dist / lastDist;
                lastDist = dist;

                let newScale = oldScale * scaleBy;
                newScale = Math.max(0.1, Math.min(5, newScale));

                // apply scale and keep the anchor under the finger-center
                this.stage.scale({ x: newScale, y: newScale });
                const newPos = {
                    x: pointer.x - this._pinchAnchor.x * newScale,
                    y: pointer.y - this._pinchAnchor.y * newScale
                };
                this.stage.position(newPos);
                this.stage.batchDraw();
            }
        }, { passive: false });

        this.stage.getContent().addEventListener('touchend', (e) => {
            if (e.touches.length < 2 && isPinching) {
                isPinching = false;
                lastDist = 0;
                this._pinchAnchor = null;
                this.stage.draggable(true);
            }
        }, { passive: false });


        this.currentBarEndX = 0;

        this.resetView();
    }



    getColorForPunch(size) {
        if (!this.punchColorMap[size]) {
            this.punchColorMap[size] = this.colorPalette[this.colorIndex % this.colorPalette.length];
            this.colorIndex++;
        }
        return this.punchColorMap[size];
    }

    getColorForMarker(value) {
        if (!this.markerColorMap[value]) {
            this.markerColorMap[value] = this.colorPalette[this.colorIndex % this.colorPalette.length];
            this.colorIndex++;
        }
        return this.markerColorMap[value];
    }

    addPoint({ serialNo, type, x, y, size, value, side }) {
        const yFinal = this.getYPosition(y, side);

        let shape;
        let color = 'black';

        if (type === 'Punching') {
            color = this.getColorForPunch(size);
            shape = new Konva.Circle({
                x: x * this.scale,
                y: yFinal,
                radius: (size / 2) * this.scale,
                fill: color,
                stroke: 'black',
                strokeWidth: 1
            });
        } else if (type === 'Marking') {
            color = this.getColorForMarker(value);
            shape = new Konva.Text({
                x: x * this.scale,
                y: yFinal,
                text: value,
                fontSize: 12 * this.scale,
                fontFamily: 'Calibri',
                fill: color
            });
            shape.offsetX(shape.width() / 2);
            shape.offsetY(shape.height() / 2);

            shape.scaleY(this.yOrientation === 'mirrored' ? -1 : 1);

        } else if (type === 'Cutting') {
            shape = new Konva.Line({
                points: [
                    x * this.scale,
                    this.baseY,
                    x * this.scale,
                    this.baseY + (this.barConfig.upHeight + this.barConfig.bottomHeight) * this.scale
                ],
                stroke: 'blue',
                strokeWidth: 2,
                dash: [5, 5],
            });
        } else {
            // console.error('Unsupported type:', type);
            return;
        }

        shape.meta = { serialNo, type, x, y, size, value, side };
        this.allShapes.push(shape);

        shape.on('mouseenter', () => {


            const text = `Sr: ${serialNo}\nType: ${type}\nX: ${x} mm\nY: ${y} mm${size ? `\nSize: ${size} mm` : ''}${value ? `\nValue: ${value}` : ''}`;
            const text2 = `Sr: ${serialNo}, Type: ${type}, X: ${x} mm, Y: ${y} mm${size ? `, Size: ${size} mm` : ''}${value ? `, Value: ${value}` : ''}`;

            if (this.tooltip) {
                this.tooltip.innerText = text;
                this.tooltip.style.display = 'block';
            }

            if (this.tooltipContainer) {
                this.tooltipContainer.innerText = text2;
            }
            this.stage.container().style.cursor = 'pointer';
        });

        shape.on('click', () => {
            // const text = `Sr: ${serialNo}\nType: ${type}\nX: ${x} mm\nY: ${y} mm${size ? `\nSize: ${size} mm` : ''}${value ? `\nValue: ${value}` : ''}`;
            if (typeof nextPointHighlight === 'function') {
                nextPointHighlight(serialNo - 1);
            }
        });

        shape.on('mouseleave', () => {

            if (this.tooltip) {
                this.tooltip.style.display = 'none';
            }

            this.stage.container().style.cursor = 'default';
        });

        shape.on('mousemove', (e) => {
            if (this.tooltip) {
                this.tooltip.style.left = e.evt.clientX + 10 + 'px';
                this.tooltip.style.top = e.evt.clientY + 10 + 'px';
            }
        });

        this.contentGroup.add(shape);
        this.layer.draw();
    }

    highlightMatchingPoints(matchFn) {
        this.allShapes.forEach(shape => {
            const isMatch = matchFn(shape.meta);
            shape.opacity(isMatch ? 1 : 0.7);
            shape.stroke(isMatch ? 'red' : 'black');
            shape.strokeWidth(isMatch ? 3 : 1);

            if (isMatch) this.lastSelected = shape;
        });

        if (!this.lastSelected) return;

        const scale = this.stage.scaleX();
        const x = this.lastSelected.meta.x * this.scale * scale;
        const stageX = this.stage.x();
        const stageWidth = this.stage.width();
        const screenX = x + stageX;

        const padding = 40;

        if (screenX < padding || screenX > stageWidth - padding) {
            const centerOffsetX = x - stageWidth / 2;

            this.stage.to({
                x: -centerOffsetX,
                y: this.stage.y(),
                duration: 0.4,
                easing: Konva.Easings.EaseInOut
            });
        }
    }



    addItemRecipe(recipePoints, quantity, itemSpacingMm = 50) {
        const maxX = Math.max(...recipePoints.map(p => p.x));

        for (let i = 0; i < quantity; i++) {
            const offsetX = this.currentBarEndX;

            recipePoints.forEach(point => {
                this.addPoint({
                    type: point.type,
                    x: point.x + offsetX,
                    y: point.y,
                    size: point.size,
                    value: point.value,
                    side: point.side || 'A'
                });
            });

            this.currentBarEndX += maxX + itemSpacingMm;
        }
    }

    resetView() {
        const scaleFitHeight = this.getFitHeightScale();
        this.stage.scale({ x: scaleFitHeight, y: scaleFitHeight });
        this.stage.position({ x: 0, y: 0 });
        this.stage.batchDraw();
    }

    panLeft() {
        const scaleFitHeight = this.getFitHeightScale();
        this.stage.scale({ x: scaleFitHeight, y: scaleFitHeight });
        this.stage.position({ x: 0, y: 0 });
        this.stage.batchDraw();
    }

    panRight() {
        const scaleFitHeight = this.getFitHeightScale();
        this.stage.scale({ x: scaleFitHeight, y: scaleFitHeight });

        const containerWidth = this.stage.width();
        const scaledWidth = this.barConfig.length * this.scale * scaleFitHeight;

        this.stage.position({
            x: containerWidth - scaledWidth,
            y: 0
        });
        this.stage.batchDraw();
    }

    panCenter() {
        const scaleFitHeight = this.getFitHeightScale();
        this.stage.scale({ x: scaleFitHeight, y: scaleFitHeight });

        const containerWidth = this.stage.width();
        const scaledWidth = this.barConfig.length * this.scale * scaleFitHeight;

        const centerX = (containerWidth - scaledWidth) / 2;

        this.stage.position({ x: centerX, y: 0 });
        this.stage.batchDraw();
    }

    fitScreen() {
        const containerWidth = this.stage.width();
        const containerHeight = this.stage.height();

        const barWidthPx = this.barConfig.length * this.scale;
        const barHeightPx = (this.barConfig.upHeight + this.barConfig.bottomHeight) * this.scale;


        const scaleFitHeight = containerHeight / barHeightPx;
        const scaleFitWidth = containerWidth / barWidthPx;

        const finalScale = Math.min(scaleFitHeight, scaleFitWidth);

        this.stage.scale({ x: finalScale, y: finalScale });

        const verticalOffset = (containerHeight - barHeightPx * finalScale) / 2;
        const horizontalOffset = (containerWidth - barWidthPx * finalScale) / 2;

        this.stage.position({ x: horizontalOffset, y: verticalOffset });
        this.stage.batchDraw();
    }

}
