import {
    Combobox,
    ComboboxButton,
    ComboboxInput,
    ComboboxOption,
    ComboboxOptions,
} from '@headlessui/react';
import { useMemo, useState } from 'react';

const VIRTUAL_THRESHOLD = 100;

function normalizeSearch(text) {
    return (text ?? '').toString().trim().toLocaleLowerCase();
}

function matchesOption(option, query) {
    if (!query) {
        return true;
    }

    const normalizedQuery = normalizeSearch(query);
    const haystack = normalizeSearch(option.searchText ?? option.label);

    return haystack.includes(normalizedQuery);
}

const optionClassName =
    'group relative cursor-default select-none py-2 ps-3 pe-9 text-gray-900 data-[focus]:bg-indigo-600 data-[focus]:text-white';

export default function SearchableSelect({
    options = [],
    value,
    onChange,
    placeholder = '',
    disabled = false,
    invalid = false,
    id,
    className = '',
    emptyMessage = 'No records found.',
    renderOption,
}) {
    const [query, setQuery] = useState('');

    const selected = useMemo(
        () => options.find((option) => String(option.value) === String(value)) ?? null,
        [options, value],
    );

    const filtered = useMemo(
        () => options.filter((option) => matchesOption(option, query)),
        [options, query],
    );

    const useVirtual = options.length > VIRTUAL_THRESHOLD;
    const virtualOptions = useVirtual ? filtered : null;

    const renderOptionContent = (option) => {
        if (renderOption) {
            return renderOption(option);
        }

        return option.label;
    };

    return (
        <Combobox
            value={selected}
            onChange={(option) => onChange(option?.value ?? '')}
            onClose={() => setQuery('')}
            disabled={disabled}
            invalid={invalid}
            virtual={virtualOptions ? { options: virtualOptions } : null}
            by={(a, b) => String(a?.value) === String(b?.value)}
        >
            <div className={`relative ${className}`}>
                <ComboboxInput
                    id={id}
                    className="block w-full rounded-md border-gray-300 py-2 pe-10 ps-3 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                    displayValue={(option) => option?.label ?? ''}
                    onChange={(event) => setQuery(event.target.value)}
                    placeholder={placeholder}
                />
                <ComboboxButton className="absolute inset-y-0 end-0 flex items-center rounded-e-md px-2 focus:outline-none">
                    <svg
                        className="h-5 w-5 text-gray-400"
                        viewBox="0 0 20 20"
                        fill="currentColor"
                        aria-hidden="true"
                    >
                        <path
                            fillRule="evenodd"
                            d="M5.23 7.21a.75.75 0 011.06.02L10 11.168l3.71-3.94a.75.75 0 111.08 1.04l-4.25 4.5a.75.75 0 01-1.08 0l-4.25-4.5a.75.75 0 01.02-1.06z"
                            clipRule="evenodd"
                        />
                    </svg>
                </ComboboxButton>

                <ComboboxOptions
                    anchor="bottom start"
                    className="z-50 mt-1 max-h-60 w-[var(--input-width)] overflow-auto rounded-md bg-white py-1 text-base shadow-lg ring-1 ring-black/5 focus:outline-none sm:text-sm empty:invisible"
                >
                    {useVirtual ? (
                        ({ option }) => (
                            <ComboboxOption key={option.value} value={option} className={optionClassName}>
                                {renderOptionContent(option)}
                            </ComboboxOption>
                        )
                    ) : filtered.length === 0 ? (
                        <div className="px-3 py-2 text-gray-500">{emptyMessage}</div>
                    ) : (
                        filtered.map((option) => (
                            <ComboboxOption key={option.value} value={option} className={optionClassName}>
                                {renderOptionContent(option)}
                            </ComboboxOption>
                        ))
                    )}
                </ComboboxOptions>
            </div>
        </Combobox>
    );
}
