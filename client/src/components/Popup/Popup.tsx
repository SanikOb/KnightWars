import React, { useContext, useState, useEffect } from 'react';
import { ServerContext } from '../../App';
import Button from '../Button/Button';
import { TError } from '../../services/server/types';

type TInnerButton = {
    isHover?: boolean;
    text: string;
    onClick: () => void;
}

export type TPopupData = {
    className?: string;
    title?: string;
    text?: string;
    closeHovered?: boolean;
    buttons?: TInnerButton[];
};

const Popup: React.FC = () => {
    const server = useContext(ServerContext);
    const [data, setData] = useState<TPopupData | null>(null);

    useEffect(() => {
        const showErrorHandler = (error: TError) => {
            const { code, text } = error;
            setData({
                title: `Ошибка №${code}`,
                text,
            });
            setTimeout(() => setData(null), 3000);
        }

        server.showError(showErrorHandler);
    });

    if (!data) return (<></>);

    const { title, text, buttons = [] } = data;

    return (<div className="popup" >
        <div className={'popup-wrapper'}>
            <div className='popup-text-block' >
                <div className="popup-title" > {title} </div>
                < div className="popup--info-text" > {text} </div>
            </div>
            < div className='buttons-block' >
                {
                    buttons.map((data, index) => {
                        const { text, onClick, isHover } = data;
                        return <Button
                            variant='main'
                            key={index}
                            text={text}
                            onClick={onClick}
                            isHover={isHover}
                        />
                    })
                }
            </div>
        </div>
    </div>);
}

export default Popup;