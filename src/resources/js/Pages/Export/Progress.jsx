import { GlobalStyles } from '@bigcommerce/big-design';
import { Page } from '@bigcommerce/big-design-patterns';

import ExportProcessingSection from '@/Sections/Export/ExportProcessingSection';

export default function Progress(props) {
    // console.log("props", props);
    return (
        <>
            <GlobalStyles />
            <Page>
                <ExportProcessingSection {...props} />
            </Page>
        </>
    );
}
