//
//  FlightSchoolCell.h
//  Space Flight
//
//  Created by Michael Stratford on 7/4/12.
//  Copyright (c) 2012 JLOOP. All rights reserved.
//

#import <UIKit/UIKit.h>

@interface FlightSchoolCell : UITableViewCell
@property (retain, nonatomic) IBOutlet UIButton *viewButton;
@property (retain, nonatomic) IBOutlet UILabel *title;
@property (retain, nonatomic) IBOutlet UILabel *completed;
@property (retain, nonatomic) IBOutlet UIImageView *imageView;

@end
