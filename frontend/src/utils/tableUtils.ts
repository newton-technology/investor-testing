import dayjs from 'dayjs';
import 'dayjs/locale/ru';
import timezone from 'dayjs/plugin/timezone';
import utc from 'dayjs/plugin/utc';

dayjs.extend(timezone);
dayjs.extend(utc);

export const dateFormatter = (UNIX_timestamp: number, format: string): string => {
    return dayjs.unix(UNIX_timestamp).tz('Europe/Moscow').locale('ru').format(format);
};

export const todayDay = dayjs(new Date()).locale('ru').tz('Europe/Moscow').format('YYYY-MM-D');

export const unixTime = (day: string, time: string): number => {
    const DayTime = `${day} ${time}`;
    const formatDate = day ? dayjs.tz(DayTime, 'Europe/Moscow').locale(`ru`).format() : ``;
    return new Date(formatDate).getTime() / 1000;
};
