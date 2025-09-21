import CONFIG from '../../../config';



// взять спрайт для обычной анимации
const getSpritesFromFrame = (frame: number[]) => {
    const count = {
        frame: 0,
        timestamp: Date.now(),
    };

    return (): number => {
        const currentTimestamp = Date.now();
        if (currentTimestamp - count.timestamp >= 100) {
            count.timestamp = currentTimestamp;
            if (count.frame >= 0) {
                count.frame++;
                if (count.frame >= frame.length) {
                    count.frame = 0;
                }
            }
        }
        return frame[count.frame];
    }
}

const useSprites = (): [HTMLImageElement[], (spriteNo: number) => number[], Array<() => number>] => {
    const { SPRITE_SIZE, LINE_OF_SPRITES } = CONFIG;
    const spritesImage = new Image();


    const getSprite = (spriteNo: number): number[] => {
        const y = Math.trunc(spriteNo / LINE_OF_SPRITES) * SPRITE_SIZE;
        const x = (spriteNo % LINE_OF_SPRITES - 1) * SPRITE_SIZE;
        return [x, y, SPRITE_SIZE];
    }

    return [
        [spritesImage],
        getSprite,
        [] // для анимации
    ];
}

export default useSprites;