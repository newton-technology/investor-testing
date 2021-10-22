import {useTablePage} from '../../pages/admin/components/hooks/useTablePage';
import {history} from '../../history/history';
import {renderHook, act} from '@testing-library/react-hooks';
import {Router} from 'react-router-dom';
import React from 'react';

describe('useTablePage', () => {
    beforeEach(() => {
        act(() => {
            history.push('/tests');
        });
    });
    const {result} = renderHook(() => useTablePage(10), {
        wrapper: ({children}) => (
            <>
                <Router history={history}>
                    <> {children}</>
                </Router>
            </>
        ),
    });

    it('should have an initial useTablePage', async () => {
        expect(result.current.page).toBe(1);

        act(() => {
            result.current.onChangePage(3);
        });
        expect(history.location.search).toBe('?page=3');
        expect(result.current.page).toBe(3);

        await act(async () => {
            history.push(`/tests?page=6`);
        });
        expect(history.location.search).toBe('?page=6');
        expect(result.current.page).toBe(6);

        await act(async () => {
            history.push(`/tests?page=12`);
        });
        expect(history.location.search).toBe('?page=12');
        expect(result.current.page).toBe(1);
    });
});
