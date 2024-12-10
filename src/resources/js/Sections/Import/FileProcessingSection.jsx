import { Box, H1, Panel, ProgressBar, Text } from '@bigcommerce/big-design';
import { router } from '@inertiajs/react';
import { useEffect } from 'react';

export default function FileProcessingSection(props) {
    useEffect(() => {
        const interval = setInterval(() => {
            router.visit('/gift-certificates/import');
        }, 5000); //set your time here. repeat every 5 seconds

        return () => clearInterval(interval);
    }, []);

    return (
        <>
            <H1>Import {props?.progress?.file_name}</H1>
            <Panel
                header="Importing gift certificates"
                description="This may take a minute if you're importing lots of gift certificates. Feel free to leave this window and check back later."
            >
                <ProgressBar percent={props?.progress?.percentage} />
                <Box marginTop="xLarge">
                    <Text>
                        Importing {props?.progress?.completed_count} of{' '}
                        {props?.progress?.total_count} gift certificate
                    </Text>
                </Box>
            </Panel>
        </>
    );
}
