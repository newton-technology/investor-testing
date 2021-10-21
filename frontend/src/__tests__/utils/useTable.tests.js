import {fireEvent, screen} from '@testing-library/react';
import {useTableDates} from '../../hooks/useTable';
import {renderInRouter} from '../../hoks/test/renderInRouter';
import {history} from '../../history/history';

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
