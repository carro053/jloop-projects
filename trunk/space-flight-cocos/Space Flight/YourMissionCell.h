//
//  YourMissionCell.h
//  Space Flight
//
//  Created by Michael Stratford on 7/2/12.
//  Copyright (c) 2012 JLOOP. All rights reserved.
//

#import <UIKit/UIKit.h>

@interface YourMissionCell : UITableViewCell

@property (retain, nonatomic) IBOutlet UIImageView *imageView;
@property (retain, nonatomic) IBOutlet UILabel *title;
@property (retain, nonatomic) IBOutlet UILabel *submitted;
@property (retain, nonatomic) IBOutlet UIButton *submitButton;
@property (retain, nonatomic) IBOutlet UIButton *editButton;
@property (retain, nonatomic) IBOutlet UIButton *playButton;

@end
