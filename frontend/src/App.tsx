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
import {PageNotFound} from './pages/PageNotFound';
import {ErrorBoundary} from './components/ErrorBoundary';

const App: React.FC = () => {
    const {isAuthenticated} = useAuthorization();

    return (
        <ThemeProvider theme={theme}>
            <GlobalStyle />
            <ErrorBoundary>
                {isAuthenticated ? (
                    <Layout>
                        <Switch>
                            <Route path='/' exact>
                                <Redirect to='/tests' />
                            </Route>
                            <Route path='/tests' exact>
                                <CategoryList />
                            </Route>
                            <Route path='/tests/:categoryId' exact>
                                <CategoryTest />
                            </Route>
                            <Route path='*'>
                                <PageNotFound />
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
            </ErrorBoundary>
        </ThemeProvider>
    );
};

export default App;
