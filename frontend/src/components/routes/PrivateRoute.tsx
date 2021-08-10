import React from 'react';
import {Route} from 'react-router';

interface IProps {
    path?: string;
}

const PrivateRoute: React.FC<IProps> = ({path, ...rest}) => {
    // TODO: check role
    if (false) {
        return null;
    }

    return <Route {...rest} path={path} render={() => null} />;
};
export default PrivateRoute;
