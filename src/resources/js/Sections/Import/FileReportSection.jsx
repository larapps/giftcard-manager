import FileReportData from '@/Components/FileReportData';
import ErrorTable from '@/Components/Table/ErrorTable';
import { Box, Button, H1, H3, Panel } from '@bigcommerce/big-design';
import { router } from '@inertiajs/react';

export default function FileReportSection(props) {
    const acknowledgeReport = () => {
        router.post(
            '/gift-certificates/import/' +
                props.progress.file_id +
                '/acknowledge',
        );
    };

    const redirectList = () => {
        router.get('/gift-certificates');
    };

    return (
        <>
            <H1>Import {props?.progress?.file_name}</H1>
            <Panel
                header="Import finished"
                description={
                    props?.progress?.completed_count +
                    ' gift certificates were processed on ' +
                    props?.progress?.updated_at
                }
                action={{
                    variant: 'secondary',
                    text: 'Start new import',
                    onClick: acknowledgeReport,
                }}
            >
                <FileReportData {...props} />

                <Box marginTop="xxLarge">
                    <Button
                        actionType="normal"
                        isLoading={false}
                        variant="primary"
                        onClick={redirectList}
                    >
                        View gift certificates
                    </Button>
                </Box>

                {props.progress.failure_count > 0 && (
                    <>
                        <Box marginTop="xLarge">
                            <H3>Error log</H3>
                        </Box>
                        <ErrorTable
                            data={props.error_log}
                            fileId={props?.progress?.file_id}
                            totalCount={props.error_log[0].total_failure_count}
                        />
                    </>
                )}
            </Panel>
        </>
    );
}
