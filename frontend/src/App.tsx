import axios from 'axios';
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

axios.get('http://localhost:9000/api/investor_testing/categories').then((c) => console.log(c));
