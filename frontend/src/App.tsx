import React from 'react';
import {Route, Switch} from 'react-router-dom';
import {ThemeProvider} from 'styled-components';

import {CategoryList} from './pages/category_list/CategoryList';
import {CategoryTest} from './pages/category_test/CategoryTest';
import {Layout} from './components/Layout';
import {theme} from './theme/theme';
import {GlobalStyle} from './theme/GlobalStyle';

const App: React.FC = () => {
    return (
        <ThemeProvider theme={theme}>
            <GlobalStyle />
            <Layout>
                <Switch>
                    <Route path='/tests' exact>
                        <CategoryList />
                    </Route>
                    <Route path='/tests/:id' exact>
                        <CategoryTest />
                    </Route>
                </Switch>
            </Layout>
        </ThemeProvider>
    );
};

export default App;
