import React from 'react';
import Button from '../../components/Button/Button';
import { IBasePage, PAGES } from '../PageManager';

const NotFound: React.FC<IBasePage> = (props: IBasePage) => {
    const { setPage } = props;

    const clickHandler = () => setPage(PAGES.LOGIN);

    return (<>
        <h1>Упс... Нету такой страницы</h1>
        <Button onClick={clickHandler} text='Назад' />
    </>)
}

export default NotFound;