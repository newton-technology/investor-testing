import dayjs from 'dayjs';
require('dayjs/locale/ru');

export const dateFormatter = (UNIX_timestamp: number, format: string): string => {
    return dayjs(UNIX_timestamp).locale('ru').format(format);
};

export const detailTestDateFormatter = (UNIX_timestamp: number): string => {
    return dayjs(UNIX_timestamp).locale('ru').format('D MMMM YYYY; H:m (МСК)');
};

export const todayDay = dayjs(new Date()).locale('ru').format('YYYY-MM-D');
export type Direction = 'asc' | 'desc';

interface ITest {
    createdAt: number;
}

export const sortComparator = (direction: Direction) => (a: ITest, b: ITest) => {
    return direction === 'desc' ? b.createdAt - a.createdAt : a.createdAt - b.createdAt;
};
