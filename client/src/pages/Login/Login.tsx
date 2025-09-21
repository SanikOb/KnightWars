import React, { useContext, useRef } from 'react';
import { ServerContext } from '../../App';
import Button from '../../components/Button/Button';
import { IBasePage, PAGES } from '../PageManager';

const Login: React.FC<IBasePage> = (props: IBasePage) => {
    const { setPage } = props;
    const server = useContext(ServerContext);
    const loginRef = useRef<HTMLInputElement>(null);
    const passwordRef = useRef<HTMLInputElement>(null);

    const loginClickHandler = async () => {
        if (loginRef.current && passwordRef.current) {
            const login = loginRef.current.value;
            const password = passwordRef.current.value;
            if (1) { // тестовое условие, чтобы логин всегда был успешный и работал без бекенда
                //if (login && password && await server.login(login, password)) {
                setPage(PAGES.GAME);
            }
        }
    }
    const backClickHandler = () => setPage(PAGES.GAME);

    return (<div className='login'>
        <div>Логин</div>
        <div className='login-wrapper'>
            <div className='login-buttons'>
                <Button onClick={loginClickHandler} text='Авторизоваться' />
                <Button onClick={backClickHandler} text='Назад' />
            </div>
        </div>
    </div>)
}

export default Login;