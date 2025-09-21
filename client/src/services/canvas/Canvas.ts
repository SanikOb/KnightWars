import { TWINDOW } from "../../config";

enum EDIRECTION {
    UP,
    DOWN,
    LEFT,
    RIGHT,
}

export type TCanvas = {
    parentId: string;
    WINDOW: TWINDOW;
    WIDTH: number;
    HEIGHT: number;
    callbacks: {
        mouseMove: (x: number, y: number) => void;
        mouseClick: (x: number, y: number) => void;
        mouseRightClick: () => void;
    },
}

class Canvas {
    parentId: string;
    // контексты и канвасы
    canvas: HTMLCanvasElement;
    context: CanvasRenderingContext2D;
    canvasV: HTMLCanvasElement;
    contextV: CanvasRenderingContext2D;
    // общая ширина и высота канвасов
    WIDTH: number;
    HEIGHT: number;
    WINDOW: TWINDOW;
    DIRECTION = {
        [EDIRECTION.UP]: -90 * Math.PI / 180,
        [EDIRECTION.DOWN]: 90 * Math.PI / 180,
        [EDIRECTION.LEFT]: 180 * Math.PI / 180,
        [EDIRECTION.RIGHT]: 0,
    }
    dx = 0;
    dy = 0;
    interval: NodeJS.Timer;
    callbacks: {
        mouseMove: (x: number, y: number) => void;
        mouseClick: (x: number, y: number) => void;
        mouseRightClick: () => void;
    }

    constructor(options: TCanvas) {
        const { parentId, WINDOW, WIDTH, HEIGHT, callbacks } = options;
        this.parentId = parentId;
        // задаём канвасы
        this.canvas = document.createElement('canvas');
        if (parentId) {
            document.getElementById(parentId)?.appendChild(this.canvas);
        } else {
            document.querySelector('body')?.appendChild(this.canvas);
        }
        this.WIDTH = WIDTH || window.innerWidth;
        this.HEIGHT = HEIGHT || window.innerHeight; // потому что иначе эта скотина добавляет вертикальный скролл
        // main canvas
        this.canvas.width = this.WIDTH;
        this.canvas.height = this.HEIGHT;
        this.context = this.canvas.getContext('2d')!;
        // virtual canvas
        this.canvasV = document.createElement('canvas');
        this.canvasV.width = this.WIDTH;
        this.canvasV.height = this.HEIGHT;
        this.contextV = this.canvasV.getContext('2d')!;
        // задаем окошко
        this.WINDOW = WINDOW;
        this.callbacks = callbacks;

        this.canvas.addEventListener('mousemove', (event) => this.mouseMoveHandler(event));
        this.canvas.addEventListener('mouseleave', () => this.mouseLeaveHandler());
        this.canvas.addEventListener('click', (event) => this.mouseClickHandler(event));
        this.canvas.addEventListener('contextmenu', (event) => this.mouseRightClickHandler(event));
        this.interval = setInterval(() => {
            if (this.dx === 0 && this.dy === 0) {
                return;
            }
            this.WINDOW.LEFT += this.dx;
            this.WINDOW.TOP += this.dy;
        }, 200);
    }

    destructor() {
        document.getElementById(this.parentId)?.removeChild(this.canvas);
        // @ts-ignore
        this.contextV = null;
        // @ts-ignore
        this.canvasV = null;
        // @ts-ignore
        this.context = null;
        // @ts-ignore
        this.canvas = null;
        clearInterval(this.interval);
    }

    mouseClickHandler(event: MouseEvent) {
        const { offsetX, offsetY } = event;
        this.callbacks.mouseClick(this.sx(offsetX), this.sy(offsetY));
    }

    mouseRightClickHandler(event: MouseEvent) {
        event.preventDefault();
        this.callbacks.mouseRightClick();
    }

    mouseMoveHandler(event: MouseEvent) {
        const { offsetX, offsetY } = event;
        // для скролла окошка относительно положения мышки
        /*
        const pX = offsetX / this.WIDTH;
        const pY = offsetY / this.HEIGHT;
        if (pX <= 0.1) {
            this.dx = -1;
        } else if (pX >= 0.9) {
            this.dx = 1;
        } else {
            this.dx = 0;
        }
        if (pY <= 0.1) {
            this.dy = -1;
        } else if (pY >= 0.9) {
            this.dy = 1;
        } else {
            this.dy = 0;
        }
        */
        this.callbacks.mouseMove(this.sx(offsetX), this.sy(offsetY));
    }

    mouseLeaveHandler() {
        this.dx = 0;
        this.dy = 0;
    }

    // перевод в экранные координаты
    xs(x: number): number {
        return (x - this.WINDOW.LEFT) / this.WINDOW.WIDTH * this.WIDTH;
    }
    ys(y: number): number {
        //return this.HEIGHT - (y - this.WINDOW.TOP) / this.WINDOW.HEIGHT * this.HEIGHT;
        return (y - this.WINDOW.TOP) / this.WINDOW.HEIGHT * this.HEIGHT; // пришлось рисовать так, потому что тайлед отдаёт в таком сраном формате
    }
    // перевод из экранных координат в локальные
    sx(x: number): number {
        return x * this.WINDOW.WIDTH / this.WIDTH + this.WINDOW.LEFT;
    }
    sy(y: number): number {
        return y * this.WINDOW.HEIGHT / this.HEIGHT + this.WINDOW.TOP;
    }

    dec(x: number): number {
        return x / this.WINDOW.WIDTH * this.WIDTH;
    }

    clear(): void {
        this.contextV.fillStyle = '#305160';
        this.contextV.fillRect(0, 0, this.WIDTH, this.HEIGHT);
    }

    clearImage(image: HTMLImageElement): void {
        this.contextV.drawImage(image, 0, 0, this.WIDTH, this.HEIGHT);
    }

    line(x1: number, y1: number, x2: number, y2: number, color = '#0f0', width = 2): void {
        this.contextV.beginPath();
        this.contextV.strokeStyle = color;
        this.contextV.lineWidth = width;
        this.contextV.moveTo(this.xs(x1), this.ys(y1));
        this.contextV.lineTo(this.xs(x2), this.ys(y2));
        this.contextV.stroke();
        this.contextV.closePath();
    }

    text(x: number, y: number, text: string, color = '#fff', font = 'bold 1rem Arial'): void {
        this.contextV.fillStyle = color;
        this.contextV.font = font;
        this.contextV.fillText(text, this.xs(x), this.ys(y));
    }

    rect(x: number, y: number, size = 64, color = '#f004'): void {
        this.contextV.fillStyle = color;
        this.contextV.fillRect(this.xs(x), this.ys(y), size, size)
    }

    // прямоугольник. НЕ квадрат
    rectangle(x: number, y: number, width = 64, height = 64, color = '#f004'): void {
        this.contextV.fillStyle = color;
        this.contextV.fillRect(this.xs(x), this.ys(y), width, height);
    }

    spriteFull(image: HTMLImageElement, dx: number, dy: number, sx: number, sy: number, size: number): void {
        this.contextV.drawImage(image, sx, sy, size, size, this.xs(dx), this.ys(dy), size, size);
    }

    // копируем изображение с виртуального канваса на основной
    render(): void {
        this.context.drawImage(this.canvasV, 0, 0);
    }
}

export default Canvas;