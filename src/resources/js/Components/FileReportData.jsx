import { Box, Flex, H1, H4, Small } from '@bigcommerce/big-design';

export default function FileReportData(props) {
    return (
        <>
            <Flex
                flexDirection="row"
                justifyContent="space-between"
                flexColumnGap="16px"
            >
                <Box
                    border="box"
                    borderRadius="normal"
                    padding="medium"
                    style={{ width: '100%' }}
                >
                    <H4>Success</H4>
                    <H1>{props?.progress?.success_count}</H1>
                    <Small>gift certificates</Small>
                </Box>

                <Box
                    border="box"
                    borderRadius="normal"
                    padding="medium"
                    style={{ width: '100%' }}
                >
                    <H4>Errors</H4>
                    <H1>{props?.progress?.failure_count}</H1>
                    <Small>gift certificates</Small>
                </Box>
            </Flex>
        </>
    );
}
