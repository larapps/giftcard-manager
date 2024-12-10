import { GlobalStyles } from '@bigcommerce/big-design';
import { Page } from '@bigcommerce/big-design-patterns';

import ExportReportSection from '@/Sections/Export/ExportReportSection';

export default function FileProgress(props) {
    // console.log("props", props);
    return (
        <>
            <GlobalStyles />
            <Page>
                <ExportReportSection {...props} />
            </Page>
        </>
    );
}
