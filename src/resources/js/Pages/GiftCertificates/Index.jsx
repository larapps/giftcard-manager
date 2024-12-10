import GiftCertificateTable from '@/Components/Table/GiftCertificateTable';
import { GlobalStyles, Panel } from '@bigcommerce/big-design';
import { ActionBar, Header, Page } from '@bigcommerce/big-design-patterns';
import { router } from '@inertiajs/react';

function Index(props) {
    const gift_certificates = props.gift_certificates;

    return (
        <>
            <GlobalStyles />

            <Page header={<Header title="Gift Certificates" />}>
                <Panel description="You can see all the gift certificates in the bigcommerce store here.">
                    <ActionBar
                        actions={[
                            {
                                text: 'Export',
                                variant: 'secondary',
                                onClick: (e) => {
                                    e.preventDefault();
                                    router.visit('/gift-certificates/export');
                                },
                            },
                            {
                                text: 'Import',
                                variant: 'secondary',
                                onClick: (e) => {
                                    e.preventDefault();
                                    router.visit('/gift-certificates/import');
                                },
                            },
                        ]}
                    />
                    <GiftCertificateTable data={gift_certificates} />
                </Panel>
            </Page>
        </>
    );
}

export default Index;
