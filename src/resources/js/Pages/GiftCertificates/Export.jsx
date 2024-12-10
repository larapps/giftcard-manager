import ExportFieldSection from '@/Sections/Export/ExportFieldSection';
import ExportSearchSection from '@/Sections/Export/ExportSearchSection';
import { GlobalStyles } from '@bigcommerce/big-design';
import { Header, Page } from '@bigcommerce/big-design-patterns';
import { router } from '@inertiajs/react';

import { useState } from 'react';

function Export() {
    const [filters, setFilters] = useState({});
    const [fields, setFields] = useState({});

    const onSubmit = () => {
        // console.log("filters", filters);
        // console.log("fields", fields);

        router.post(
            '/gift-certificates/export/start',
            {
                filters: filters,
                fields: fields,
            },
            {
                replace: true,
                preserveState: false,
            },
        );
    };

    return (
        <>
            <GlobalStyles />

            <Page header={<Header title="Export" />}>
                <ExportSearchSection
                    setFilters={setFilters}
                    onSubmit={onSubmit}
                />
                <ExportFieldSection setFields={setFields} onSubmit={onSubmit} />
            </Page>
        </>
    );
}

export default Export;
