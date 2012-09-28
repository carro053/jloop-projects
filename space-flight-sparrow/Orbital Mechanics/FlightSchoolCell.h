//
//  FlightSchoolCell.h
//  Space Flight
//
//  Created by Michael Stratford on 7/4/12.
//  Copyright (c) 2012 JLOOP. All rights reserved.
//

#import <UIKit/UIKit.h>

@interface FlightSchoolCell : UITableViewCell
@property (strong, nonatomic) IBOutlet UIButton *viewButton;
@property (strong, nonatomic) IBOutlet UILabel *title;
@property (strong, nonatomic) IBOutlet UILabel *completed;
@property (strong, nonatomic) IBOutlet UIImageView *imageView;

@end
