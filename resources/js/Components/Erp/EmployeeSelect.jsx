import SearchableSelect from '@/Components/Erp/SearchableSelect';
import useTranslation from '@/hooks/useTranslation';
import { resourceItems } from '@/utils/resource';
import { useMemo } from 'react';

function buildEmployeeOptions(employees, extraEmployee = null) {
    const seen = new Set();
    const options = [];

    const add = (employee) => {
        if (!employee?.id || seen.has(employee.id)) {
            return;
        }

        seen.add(employee.id);
        options.push({
            value: employee.id,
            label: employee.name,
            position: employee.position || '',
            searchText: [employee.name, employee.position, employee.father_name].filter(Boolean).join(' '),
        });
    };

    if (extraEmployee) {
        add(extraEmployee);
    }

    resourceItems(employees).forEach(add);

    return options.sort((a, b) => a.label.localeCompare(b.label, undefined, { sensitivity: 'base' }));
}

export default function EmployeeSelect({ employees, value, onChange, error, selectedEmployee = null, className = '' }) {
    const { t } = useTranslation();

    const options = useMemo(
        () => buildEmployeeOptions(employees, selectedEmployee),
        [employees, selectedEmployee],
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
                    {option.position ? (
                        <span className="block truncate text-xs text-gray-500 group-data-[focus]:text-indigo-200">
                            {option.position}
                        </span>
                    ) : null}
                </>
            )}
        />
    );
}
