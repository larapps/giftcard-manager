import { GlobalStyles } from '@bigcommerce/big-design';
import { Page } from '@bigcommerce/big-design-patterns';

import FileProcessingSection from '@/Sections/Import/FileProcessingSection';

export default function FileProgress(props) {
    // console.log("props", props);
    return (
        <>
            <GlobalStyles />
            <Page>
                <FileProcessingSection {...props} />
            </Page>
        </>
    );
}
