import { forwardRef, useEffect, useImperativeHandle, useRef } from 'react';
import { normalizeDigits } from '@/utils/digits';

const SKIP_DIGIT_NORMALIZE = new Set(['password', 'email']);

export default forwardRef(function TextInput(
    { type = 'text', className = '', isFocused = false, onChange, inputMode, ...props },
    ref,
) {
    const localRef = useRef(null);
    const isNumeric = type === 'number';
    const resolvedType = isNumeric ? 'text' : type;
    const resolvedInputMode = inputMode ?? (isNumeric
        ? (props.step?.toString().includes('.') ? 'decimal' : 'numeric')
        : undefined);

    useImperativeHandle(ref, () => ({
        focus: () => localRef.current?.focus(),
    }));

    useEffect(() => {
        if (isFocused) {
            localRef.current?.focus();
        }
    }, [isFocused]);

    const handleChange = (event) => {
        if (!onChange) {
            return;
        }

        if (SKIP_DIGIT_NORMALIZE.has(type)) {
            onChange(event);
            return;
        }

        const normalized = normalizeDigits(event.target.value);

        if (normalized === event.target.value) {
            onChange(event);
            return;
        }

        onChange({
            ...event,
            target: { ...event.target, value: normalized },
        });
    };

    return (
        <input
            {...props}
            type={resolvedType}
            inputMode={resolvedInputMode}
            onChange={handleChange}
            className={
                'rounded-lg border-slate-300 bg-white shadow-sm transition focus:border-indigo-500 focus:ring-indigo-500 ' +
                className
            }
            ref={localRef}
        />
    );
});
