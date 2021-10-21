import {fireEvent, screen} from '@testing-library/react';
import {useTableDates, useTablePage} from '../../pages/admin/components/hooks/useTable';
import {renderInRouter} from '../../hoks/test/renderInRouter';
import {history} from '../../history/history';
import {renderHook, act} from '@testing-library/react-hooks';
import {Router} from 'react-router-dom';
import React from 'react';

function ReactMock() {
    const {datesValue, onDateChange} = useTableDates();

    return (
        <>
            <input data-testid='dateStart' value={datesValue.dateStart} name='dateStart' onChange={onDateChange} />
            <input data-testid='dateEnd' value={datesValue.dateEnd} name='dateEnd' onChange={onDateChange} />;
        </>
    );
}

const render = () => renderInRouter(ReactMock);

describe('Set dates using the useTableDates hook', () => {
    beforeEach(() => {
        history.push('/');
    });
    render();
    const inputStart = screen.getByTestId('dateStart');
    const inputEnd = screen.getByTestId('dateEnd');

    it('Hook returned a start date similar to the input data.', () => {
        fireEvent.change(inputStart, {target: {value: '2021-10-09'}});
        expect(inputStart.value).toBe('2021-10-09');
    });

    it('Hook returned a end date similar to the input data.', () => {
        fireEvent.change(inputEnd, {target: {value: '2021-10-08'}});
        expect(inputEnd.value).toBe('2021-10-08');
    });
});

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
