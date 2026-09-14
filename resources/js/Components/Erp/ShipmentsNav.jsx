import useTranslation from '@/hooks/useTranslation';
import usePermission from '@/hooks/usePermission';
import { Link } from '@inertiajs/react';

const links = [
    { route: 'shipments.index', key: 'shipments.title' },
    { route: 'mine-types.index', key: 'mine_types.title', permission: 'mine-types.view' },
    { route: 'stone-types.index', key: 'stone_types.title', permission: 'stone-types.view' },
];

export default function ShipmentsNav({ active }) {
    const { t } = useTranslation();
    const { can } = usePermission();

    return (
        <div className="mb-4 flex flex-wrap gap-2">
            {links.filter((item) => !item.permission || can(item.permission)).map((item) => (
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
