import { Table } from '@bigcommerce/big-design';
import { ActionBar } from '@bigcommerce/big-design-patterns';
import axios from 'axios';
import { useState } from 'react';

export default function GiftCertificateTable(data) {
    // console.log("data da"+JSON.stringify(data.data));

    const [currentPage, setCurrentPage] = useState(1);
    const [tableData, setTableData] = useState(data.data);
    const [loadDisabled, setLoadDisabled] = useState(false);
    const [buttonLoading, setButtonLoading] = useState(false);

    const formatDate = (date) => {
        // multiplied by 1000 so that the argument is in milliseconds, not seconds
        let purchaseDate = new Date(date * 1000);
        return (
            purchaseDate.getFullYear() +
            '-' +
            (purchaseDate.getMonth() + 1) +
            '-' +
            purchaseDate.getDate()
        );
    };

    const loadNext = (e) => {
        e.preventDefault();
        setButtonLoading(true);
        axios
            .get(
                '/gift-certificates/load?page=' +
                    (currentPage + 1) +
                    '&limit=15',
            )
            .then((response) => {
                if (response.status === 200) {
                    if (Object.values(response.data).length === 0) {
                        setLoadDisabled(true);
                    } else {
                        let newData = tableData;
                        setTableData(newData.concat(response.data));
                        setCurrentPage(currentPage + 1);
                    }
                }
                setButtonLoading(false);
            });
    };

    return (
        <>
            <Table
                columns={[
                    { header: 'BC ID', hash: 'id', render: ({ id }) => id },
                    {
                        header: 'Code',
                        hash: 'code',
                        render: ({ code }) => code,
                    },
                    {
                        header: 'Amount',
                        hash: 'amount',
                        render: ({ amount }) => parseFloat(amount).toFixed(2),
                    },
                    {
                        header: 'Balance',
                        hash: 'balance',
                        render: ({ balance }) => parseFloat(balance).toFixed(2),
                    },
                    {
                        header: 'Date Purchased',
                        hash: 'date_purchased',
                        render: ({ purchase_date }) =>
                            formatDate(purchase_date),
                    },
                    {
                        header: 'Expiry Date ',
                        hash: 'expiry_date',
                        render: ({ expiry_date }) => formatDate(expiry_date),
                    },
                    {
                        header: 'Status',
                        hash: 'status',
                        render: ({ status }) => status,
                    },
                ]}
                itemName="Gift Certificates"
                keyField="id"
                items={tableData}
                stickyHeader
            />

            <ActionBar
                actions={[
                    {
                        text: 'Load More',
                        variant: 'primary',
                        onClick: (e) => {
                            loadNext(e);
                        },
                        disabled: loadDisabled,
                        isLoading: buttonLoading,
                    },
                ]}
            />
        </>
    );
}
