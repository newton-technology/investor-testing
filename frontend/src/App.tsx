import React from 'react';
import {Route, Switch, Redirect} from 'react-router-dom';
import {ThemeProvider} from 'styled-components';

import {Layout} from './components/Layout';
import {useAuthorization} from './hooks/useAuthorization';
import {AllTestsPage} from './pages/admin/AllTestsPage';
import {AuthorizationPage} from './pages/AuthorizationPage';
import {CategoryList} from './pages/category_list/CategoryList';
import {CategoryTest} from './pages/category_test/CategoryTest';
import {GlobalStyle} from './theme/GlobalStyle';
import {theme} from './theme/theme';
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
                        <Route path='/tests/:id' exact>
                            <CategoryTest />
                        </Route>
                    </Switch>
                </Layout>
            ) : (
                <Switch>
                    <Route path='/' exact>
                        <AuthorizationPage />
                    </Route>
                    <Route path='/admin/tests'>
                        <Layout>
                            <AllTestsPage />
                        </Layout>
                    </Route>
                    <Route path='*'>
                        <Redirect to='/' />
                    </Route>
                </Switch>
            )}
        </ThemeProvider>
    );
};

export default App;
