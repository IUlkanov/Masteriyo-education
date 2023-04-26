import {
	Box,
	Skeleton,
	SkeletonCircle,
	SkeletonText,
	Stack,
} from '@chakra-ui/react';
import React from 'react';

const Dashboard: React.FC = () => {
	const countBox = [1, 2, 3];
	return (
		<Stack direction="column" spacing="10">
			<Stack
				direction={{ base: 'column', sm: 'column', md: 'row', lg: 'row' }}
				spacing="5">
				{countBox.map((x) => (
					<Box p={10} borderWidth="1px" key={x} w="100%">
						<Stack direction="row">
							<Skeleton h="4rem" w="4rem" />
							<Stack flex={2} spacing="5">
								<SkeletonText noOfLines={1} />
								<SkeletonCircle size="4" />
							</Stack>
						</Stack>
					</Box>
				))}
			</Stack>
			<Stack
				direction={{ base: 'column', sm: 'row', md: 'row', lg: 'row' }}
				spacing="4"
				justify={{
					base: 'start',
					sm: 'start',
					md: 'start',
					lg: 'space-between',
				}}
				alignItems={{
					base: 'left',
					sm: 'left',
					md: 'center',
					lg: 'center',
				}}>
				<SkeletonText noOfLines={1} w="70px" />
				<SkeletonText noOfLines={1} w="70px" />
			</Stack>
			{countBox.map((x) => (
				<Box key={x} p="5" border="1px" borderColor="gray.100">
					<Stack direction="row" spacing="5">
						<Skeleton h="5rem" w="5rem" />
						<Stack direction="column" flex={1}>
							<SkeletonText spacing={4} />
						</Stack>
					</Stack>
				</Box>
			))}
		</Stack>
	);
};

export default Dashboard;
