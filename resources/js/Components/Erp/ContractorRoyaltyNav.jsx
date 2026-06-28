import useTranslation from '@/hooks/useTranslation';
import { Link } from '@inertiajs/react';

const links = [
    { route: 'contractor-royalty.productions.index', key: 'contractor_royalty.productions' },
    { route: 'contractor-royalty.payments.index', key: 'contractor_royalty.payments' },
    { route: 'contractor-royalty.ledger', key: 'contractor_royalty.ledger' },
];

export default function ContractorRoyaltyNav({ active }) {
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
