import { GlobalStyles } from '@bigcommerce/big-design';
import { Page } from '@bigcommerce/big-design-patterns';

import FilePreviewSection from '@/Sections/Import/FilePreviewSection';
import FileProcessingSection from '@/Sections/Import/FileProcessingSection';
import FileReportSection from '@/Sections/Import/FileReportSection';
import FileUploadSection from '@/Sections/Import/FileUploadSection';

import { router } from '@inertiajs/react';
import { useState } from 'react';

function Import() {
    const [currentStep, setCurrentStep] = useState(0);
    const [previewData, setPreviewData] = useState([]);
    const [headerData, setHeaderData] = useState([]);
    const [filesData, setFilesData] = useState([]);

    const nextStep = () => {
        setCurrentStep(currentStep + 1);
    };

    const previousStep = () => {
        setCurrentStep(currentStep - 1);
    };

    const changeFile = (files) => {
        setFilesData(files);
    };

    const handleSubmit = () => {
        // console.log("sample");
        // console.log("files",filesData);
        let params = new FormData();
        params.append('import_file', filesData[0]);
        router.post('/gift-certificates/import', params, {
            replace: true,
            preserveState: false,
        });
    };

    return (
        <>
            <GlobalStyles />

            <Page>
                {(currentStep === 0 && (
                    <FileUploadSection
                        nextStep={nextStep}
                        previousStep={previousStep}
                        setPreviewData={setPreviewData}
                        setHeaderData={setHeaderData}
                        changeFile={changeFile}
                    />
                )) ||
                    (currentStep === 1 && (
                        <FilePreviewSection
                            nextStep={nextStep}
                            previousStep={previousStep}
                            previewData={previewData}
                            headerData={headerData}
                            handleSubmit={handleSubmit}
                            filesData={filesData}
                        />
                    )) ||
                    (currentStep === 2 && (
                        <FileProcessingSection
                            nextStep={nextStep}
                            previousStep={previousStep}
                        />
                    )) ||
                    (currentStep === 3 && (
                        <FileReportSection
                            nextStep={nextStep}
                            previousStep={previousStep}
                        />
                    ))}
            </Page>
        </>
    );
}

export default Import;
