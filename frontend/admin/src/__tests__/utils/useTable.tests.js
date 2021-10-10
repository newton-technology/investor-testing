import {render, fireEvent, getByTestId} from '@testing-library/react';
import {useTableDates} from '../../hooks/useTable';

function ReactMock() {
    const {datesValue, onDateChange} = useTableDates();

    const html = (
        <>
            <input data-testid='dateStart' value={datesValue.dateStart} name='dateStart' onChange={onDateChange} />
            <input data-testid='dateEnd' value={datesValue.dateEnd} name='dateEnd' onChange={onDateChange} />;
        </>
    );
    return <>{html}</>;
}

describe('Set dates using the useTableDates hook', () => {
    const {container} = render(<ReactMock />);
    const inputStart = getByTestId(container, 'dateStart');
    const inputEnd = getByTestId(container, 'dateEnd');

    it('Hook returned a start date similar to the input data.', () => {
        fireEvent.change(inputStart, {target: {value: '2021-10-09'}});
        expect(inputStart.value).toBe('2021-10-09');
    });

    it('Hook returned a end date similar to the input data.', () => {
        fireEvent.change(inputEnd, {target: {value: '2021-10-08'}});
        expect(inputEnd.value).toBe('2021-10-08');
    });
});
