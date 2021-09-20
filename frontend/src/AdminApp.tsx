import React from 'react';
import {Route, Switch, Redirect} from 'react-router-dom';
import {ThemeProvider} from 'styled-components';

import {ErrorBoundary} from './components/ErrorBoundary';
import {Layout} from './components/Layout';
import {Loader} from './components/Loader';
import {useAuthorization} from './hooks/useAuthorization';
import {AllTestsPage} from './pages/admin/AllTestsPage';
import {UserTestsPage} from './pages/admin/UserTestsPage';
import {AdminAuthorization} from './pages/authorization/AdminAuthorization';
import {PageNotFound} from './pages/PageNotFound';
import {GlobalStyle} from './theme/GlobalStyle';
import {theme} from './theme/theme';

const App: React.FC = () => {
    const {isAuthenticated, isAuthLoading} = useAuthorization();

    if (isAuthLoading) {
        return (
            <ThemeProvider theme={theme}>
                <GlobalStyle />
                <ErrorBoundary>
                    <Loader isFullScreen />
                </ErrorBoundary>
            </ThemeProvider>
        );
    }

    return (
        <ThemeProvider theme={theme}>
            <GlobalStyle />
            <ErrorBoundary>
                {isAuthenticated ? (
                    <Layout isAdmin>
                        <Switch>
                            <Route path='/' exact>
                                <Redirect to='/tests' />
                            </Route>
                            <Route path='/tests' exact>
                                <AllTestsPage />
                            </Route>
                            <Route path='/test/:id'>
                                <UserTestsPage />
                            </Route>
                            <Route path='*'>
                                <PageNotFound />
                            </Route>
                        </Switch>
                    </Layout>
                ) : (
                    <Switch>
                        <Route path='/' exact>
                            <AdminAuthorization />
                        </Route>
                        <Route path='*'>
                            <Redirect to='/admin' />
                        </Route>
                    </Switch>
                )}
            </ErrorBoundary>
        </ThemeProvider>
    );
};

export default App;
