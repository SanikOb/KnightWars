import React, { useState } from 'react';

import Login from './Login/Login';
import GamePage from './Game/Game';
import NotFound from './NotFound/NotFound';

export enum PAGES {
    LOGIN,
    GAME,
    NOT_FOUND,
}

export interface IBasePage {
    setPage: (name: PAGES) => void
}

const PageManager: React.FC = () => {
    const [page, setPage] = useState<PAGES>(PAGES.LOGIN);

    return (
        <>
            {page === PAGES.LOGIN && <Login setPage={setPage} />}
            {page === PAGES.GAME && <GamePage setPage={setPage} />}
            {page === PAGES.NOT_FOUND && <NotFound setPage={setPage} />}
        </>
    );
}

export default PageManager;