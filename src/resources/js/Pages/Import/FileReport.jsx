import { GlobalStyles } from '@bigcommerce/big-design';
import { Page } from '@bigcommerce/big-design-patterns';

import FileReportSection from '@/Sections/Import/FileReportSection';

export default function FileProgress(props) {
    // console.log("props", props);
    return (
        <>
            <GlobalStyles />
            <Page>
                <FileReportSection {...props} />
            </Page>
        </>
    );
}
