import WorksheetPreview from '@/Components/WorksheetPreview';
import { Box, Button, Flex, H1 } from '@bigcommerce/big-design';

export default function FilePreviewSection(props) {
    return (
        <>
            <Flex flexDirection="row" justifyContent="space-between">
                <H1>Import {props?.filesData[0]?.name}</H1>
                <Button variant="secondary" onClick={props.previousStep}>
                    Change File
                </Button>
            </Flex>

            <Box backgroundColor="white" borderRadius="normal" padding="medium">
                <WorksheetPreview {...props} />
            </Box>

            <Flex justifyContent="flex-end">
                <Box marginTop="xxLarge">
                    <Button variant="primary" onClick={props.handleSubmit}>
                        Start Import
                    </Button>
                </Box>
            </Flex>
        </>
    );
}
