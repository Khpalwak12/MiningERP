import { useId } from 'react';
import InputLabel from '@/Components/InputLabel';
import InputError from '@/Components/InputError';
import TextInput from '@/Components/TextInput';

const inputClass =
    'mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500';

export default function ShamsiDateInput({ label, value, onChange, error, required = false, className = '' }) {
    const inputId = useId();

    return (
        <div className={className}>
            {label && <InputLabel htmlFor={inputId} value={label} required={required} />}
            <TextInput
                id={inputId}
                value={value || ''}
                onChange={(e) => onChange(e.target.value)}
                placeholder="1404/01/01"
                className={inputClass}
            />
            {error && <InputError message={error} className="mt-1" />}
        </div>
    );
}
