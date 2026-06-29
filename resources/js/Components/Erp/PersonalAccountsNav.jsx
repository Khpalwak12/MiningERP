import useTranslation from '@/hooks/useTranslation';
import { Link } from '@inertiajs/react';

const links = [
    { route: 'personal-accounts.contacts.index', key: 'personal_accounts.contacts' },
    { route: 'personal-accounts.home-expenses.index', key: 'personal_accounts.home_expenses' },
];

export default function PersonalAccountsNav({ active }) {
    const { t } = useTranslation();

    return (
        <div className="mb-4 flex flex-wrap gap-2">
            {links.map((item) => (
                <Link
                    key={item.route}
                    href={route(item.route)}
                    className={`rounded-md px-3 py-2 text-sm font-medium ${
                        active === item.route
                            ? 'bg-indigo-600 text-white'
                            : 'bg-white text-indigo-700 shadow hover:bg-indigo-50'
                    }`}
                >
                    {t(item.key)}
                </Link>
            ))}
        </div>
    );
}
