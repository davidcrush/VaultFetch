const UNITS = [
    ['year', 60 * 60 * 24 * 365],
    ['month', 60 * 60 * 24 * 30],
    ['week', 60 * 60 * 24 * 7],
    ['day', 60 * 60 * 24],
    ['hour', 60 * 60],
    ['minute', 60],
    ['second', 1],
];

function formatRelativeTime(isoDate) {
    const date = new Date(isoDate);
    const now = Date.now();
    const diffSeconds = Math.round((date.getTime() - now) / 1000);
    const absSeconds = Math.abs(diffSeconds);

    const formatter = new Intl.RelativeTimeFormat(undefined, { numeric: 'auto' });

    for (const [unit, secondsInUnit] of UNITS) {
        if (absSeconds >= secondsInUnit || unit === 'second') {
            const value = Math.round(diffSeconds / secondsInUnit);

            return formatter.format(value, unit);
        }
    }

    return formatter.format(0, 'second');
}

function formatLocalDateTime(isoDate) {
    const date = new Date(isoDate);

    return new Intl.DateTimeFormat(undefined, {
        dateStyle: 'medium',
        timeStyle: 'short',
    }).format(date);
}

export function initRelativeTimes() {
    document.querySelectorAll('[data-relative-time]').forEach((element) => {
        const iso = element.getAttribute('datetime');

        if (! iso) {
            return;
        }

        element.textContent = formatRelativeTime(iso);
        element.setAttribute('title', formatLocalDateTime(iso));
    });
}
