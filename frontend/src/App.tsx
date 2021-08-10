import React from 'react';
import {Route, Switch} from 'react-router-dom';

import Authorization from './pages/Authorization';
import './App.css';

const App: React.FC = () => {
    return (
        <Switch>
            <Route path='/' component={Authorization} exact />
        </Switch>
    );
};

export default App;
