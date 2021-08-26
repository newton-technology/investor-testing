import React from 'react';
import {Route, Switch, Redirect} from 'react-router-dom';
import {ThemeProvider} from 'styled-components';

import {CategoryList} from './pages/category_list/CategoryList';
import {CategoryTest} from './pages/category_test/CategoryTest';
import {AuthorizationPage} from './pages/AuthorizationPage';
import {Layout} from './components/Layout';
import {theme} from './theme/theme';
import {GlobalStyle} from './theme/GlobalStyle';
import {useAuthorization} from './hooks/useAuthorization';
import './api/AuthService';

const App: React.FC = () => {
    const {isAuthenticated} = useAuthorization();

    return (
        <ThemeProvider theme={theme}>
            <GlobalStyle />
            {isAuthenticated ? (
                <Layout>
                    <Switch>
                        <Route path='/' exact>
                            <Redirect to='/tests' />
                        </Route>
                        <Route path='/tests' exact>
                            <CategoryList />
                        </Route>
                        <Route path='/tests/:categoryId'>
                            <CategoryTest />
                        </Route>
                    </Switch>
                </Layout>
            ) : (
                <Switch>
                    <Route path='/'>
                        <AuthorizationPage />
                    </Route>
                </Switch>
            )}
        </ThemeProvider>
    );
};

export default App;
