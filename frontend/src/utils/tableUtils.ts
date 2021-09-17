import dayjs from 'dayjs';
import 'dayjs/locale/ru';

export const dateFormatter = (UNIX_timestamp: number, format: string): string => {
    return dayjs(UNIX_timestamp).locale('ru').format(format);
};
export const todayDay = dayjs(new Date()).locale('ru').format('YYYY-MM-D');
