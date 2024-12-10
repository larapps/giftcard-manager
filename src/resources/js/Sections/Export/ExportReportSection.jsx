import { Button, H1, Panel } from '@bigcommerce/big-design';
import { FileDownloadIcon } from '@bigcommerce/big-design-icons';
import { router } from '@inertiajs/react';

export default function ExportReportSection(props) {
    const acknowledgeReport = () => {
        router.post(
            '/gift-certificates/export/' +
                props?.file?.file_id +
                '/acknowledge',
        );
    };

    const fileDownload = () => {
        // console.log("download");
        window.location.href =
            '/gift-certificates/export/file/' + props?.file?.download_file;
    };

    return (
        <>
            <H1>Export</H1>
            <Panel
                header="Export finished"
                description={
                    props?.file?.total_count +
                    ' gift certificates were processed on ' +
                    props?.file?.updated_at
                }
                action={{
                    variant: 'secondary',
                    text: 'Start new export',
                    onClick: acknowledgeReport,
                }}
            >
                <Button
                    variant="secondary"
                    iconLeft={<FileDownloadIcon />}
                    onClick={fileDownload}
                >
                    {props?.file?.download_file.replace('exports/', '')}
                </Button>
            </Panel>
        </>
    );
}
