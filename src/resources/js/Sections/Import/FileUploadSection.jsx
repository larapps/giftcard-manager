import FileUploadForm from '@/Components/FileUploadForm';
import { Panel } from '@bigcommerce/big-design';
import { FileDownloadIcon } from '@bigcommerce/big-design-icons';
import { Header } from '@bigcommerce/big-design-patterns';

export default function FileUploadSection(props) {
    const fileDownload = () => {
        window.location.href = '/gift-certificates/import/csv-template';
    };

    return (
        <>
            <Header title="Import" />

            <Panel
                marginTop="xLarge"
                action={{
                    iconLeft: <FileDownloadIcon />,
                    variant: 'subtle',
                    text: 'csv template',
                    onClick: fileDownload,
                }}
                description="Importing lets you quickly add a lot of gift certificates to your store from a csv file."
                header="Upload import file"
            >
                <FileUploadForm {...props} />
            </Panel>
        </>
    );
}
