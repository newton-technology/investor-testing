import React from 'react';
import {Route, Switch} from 'react-router-dom';

import {useAuthorization} from './hooks/useAuthorization';
import {AuthorizationPage} from './pages/AuthorizationPage';
import './App.css';
import './api/AuthService';

const App: React.FC = () => {
    useAuthorization();
    return (
        <Switch>
            <Route path='/' component={AuthorizationPage} exact />
        </Switch>
    );
};

export default App;
