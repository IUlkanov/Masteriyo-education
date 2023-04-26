import {
	Flex,
	List,
	ListItem,
	SkeletonCircle,
	SkeletonText,
} from '@chakra-ui/react';
import React from 'react';

const Sidebar = () => {
	const length = [1, 2, 3, 4, 5, 6];
	return (
		<List
			mr={{ base: '0', sm: '0', md: '4', lg: '6' }}
			flex={{
				base: '0 0 30px',
				sm: '0 0 50px',
				md: '0 0 90px',
				lg: '0 0 200px',
			}}
			spacing="6">
			{length.map((x) => (
				<ListItem key={x}>
					<Flex
						direction={{ xs: 'column', sm: 'column', md: 'column', lg: 'row' }}
						alignItems="center"
						gap={2}>
						<SkeletonCircle size="4" />
						<SkeletonText
							noOfLines={1}
							flex={12}
							display={{ base: 'none', sm: 'none', md: 'block', lg: 'block' }}
						/>
					</Flex>
				</ListItem>
			))}
		</List>
	);
};

export default Sidebar;
