import { Link } from '@chakra-ui/react';
import { __ } from '@wordpress/i18n';
import React from 'react';
import { Td, Tr } from 'react-super-responsive-table';

interface Props {
	order: string;
	date: string;
	status: string;
	total: number | string;
}

const OrderHistoryItem: React.FC<Props> = ({ order, date, status, total }) => {
	const getStatusColor = (status: any) => {
		if (status === 'Completed') {
			return { color: 'green' };
		} else {
			return { color: 'red' };
		}
	};

	return (
		<Tr>
			<Td>
				<Link
					to="#"
					color="primary.900"
					fontWeight="bold"
					textDecoration="none">
					{order}
				</Link>
			</Td>
			<Td>{date}</Td>
			<Td style={getStatusColor(status)}>{status}</Td>
			<Td>{total} for 1 item</Td>
			<Td>
				<Link to="#" color="primary.600" textDecoration="underline">
					{__('View', 'masteriyo')}
				</Link>
			</Td>
		</Tr>
	);
};

export default OrderHistoryItem;
