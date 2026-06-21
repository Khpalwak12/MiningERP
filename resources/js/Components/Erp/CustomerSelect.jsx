import SearchableSelect from '@/Components/Erp/SearchableSelect';
import useTranslation from '@/hooks/useTranslation';
import { resourceItems } from '@/utils/resource';
import { useMemo } from 'react';

function buildCustomerOptions(customers, extraCustomer = null) {
    const seen = new Set();
    const options = [];

    const add = (customer) => {
        if (!customer?.id || seen.has(customer.id)) {
            return;
        }

        seen.add(customer.id);
        options.push({
            value: customer.id,
            label: customer.name,
            ownerName: customer.owner_name || '',
            searchText: [customer.name, customer.owner_name].filter(Boolean).join(' '),
        });
    };

    if (extraCustomer) {
        add(extraCustomer);
    }

    resourceItems(customers).forEach(add);

    return options.sort((a, b) => a.label.localeCompare(b.label, undefined, { sensitivity: 'base' }));
}

export default function CustomerSelect({ customers, value, onChange, error, selectedCustomer = null, className = '' }) {
    const { t } = useTranslation();

    const options = useMemo(
        () => buildCustomerOptions(customers, selectedCustomer),
        [customers, selectedCustomer],
    );

    return (
        <SearchableSelect
            className={className}
            options={options}
            value={value}
            onChange={onChange}
            placeholder={`${t('actions.search')}...`}
            emptyMessage={t('messages.no_records')}
            invalid={Boolean(error)}
            renderOption={(option) => (
                <>
                    <span className="block truncate font-medium">{option.label}</span>
                    {option.ownerName ? (
                        <span className="block truncate text-xs text-gray-500 group-data-[focus]:text-indigo-200">
                            {option.ownerName}
                        </span>
                    ) : null}
                </>
            )}
        />
    );
}
