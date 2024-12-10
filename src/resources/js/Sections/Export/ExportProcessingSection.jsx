import { Box, H1, Panel, ProgressBar, Text } from '@bigcommerce/big-design';
import { router } from '@inertiajs/react';
import { useEffect } from 'react';

export default function ExportProcessingSection(props) {
    useEffect(() => {
        const interval = setInterval(() => {
            router.visit('/gift-certificates/export');
        }, 5000); //set your time here. repeat every 5 seconds

        return () => clearInterval(interval);
    }, []);

    return (
        <>
            <H1>Export</H1>
            <Panel
                header="Exporting gift certificates"
                description="This may take a minute if you're exporting lots of gift certificates. Feel free to leave this window and check back later."
            >
                <ProgressBar />

                <Box marginTop="xLarge">
                    <Text>
                        Exported {props?.file?.total_count} gift certificates
                    </Text>
                </Box>
            </Panel>
        </>
    );
}
