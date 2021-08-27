import React from 'react';
import {Route, Switch, Redirect} from 'react-router-dom';
import {ThemeProvider} from 'styled-components';

import {Layout} from './components/Layout';
import {useAuthorization} from './hooks/useAuthorization';
import {Authorization} from './pages/authorization/Authorization';
import {CategoryList} from './pages/category_list/CategoryList';
import {CategoryTest} from './pages/category_test/CategoryTest';
import {PageNotFound} from './pages/PageNotFound';
import {ErrorBoundary} from './components/ErrorBoundary';
import {GlobalStyle} from './theme/GlobalStyle';
import {theme} from './theme/theme';

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
                            <Authorization />
                        </Route>
                    </Switch>
                )}
            </ErrorBoundary>
        </ThemeProvider>
    );
};

export default App;
